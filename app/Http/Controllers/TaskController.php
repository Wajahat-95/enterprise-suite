<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Validation\Rule;


class TaskController extends Controller
{
    // Method 1: Fetch all tasks from the database and return them as JSON
    public function index() {
        // 1. Get the authenticated user object
        $user = Auth::user();

        // 2. Use the relationship to getch only their tasks
        $tasks = $user->tasks()->select(['id', 'title', 'status', 'priority', 'due_date', 'is_completed'])
        ->latest()
        ->get();

        return response()->json($tasks);
    }

    public function store(Request $request) {
        // 1. Validate the incoming request data
        $validated = $request->validate([
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'due_date' => 'nullable|date|after_or_equal:today',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50'
        ]);

        try {
            $task = Auth::user()->tasks()->create($validated);
            Log::info('Task created', ['task_id' => $task->id, 'user_id' => Auth::id()]);
            
            return redirect()->route('dashboard')->with('success', 'Task created successfully!');


        } catch (\Exception $e) {
            Log::error('Task creation failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create task. Please try again.']);
        }
    }

    public function dashboard(Request $request) {
        $user = Auth::user();

        // 1. Start the Base Query (secured to the user)
        $query = $user->tasks()->latest();

        // Filter by status

        if($request->filled('status')) {
            $status = $request->status;
            if($status === 'completed') {
                $query->completed();
            } elseif ($status === 'pending') {
                $query->pending();
            } elseif ($status === 'overdue') {
                $query->overdue();
            } elseif (in_array($status, ['in_progress', 'cancelled'])) {
                $query->status($status);
            }
        }

        // Filter by priority
        if($request->filled('priority')) {
            $query->priority($request->priority);
        }

        // Search
        if($request->filled('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if(in_array($sortField, ['title', 'priority', 'due_date', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        
        $tasks = $query->paginate(10)->withQueryString();

        // Get statistics
        $stats = [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->completed()->count(),
            'pending' => $user->tasks()->pending()->count(),
            'overdue' => $user->tasks()->overdue()->count(),
        ];

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'stats' => $stats,
            'filters' => $request->only(['status', 'priority', 'search', 'sort', 'direction']),
        ]);
    }

    public function update(Request $request, Task $task) {
        // 1. Security Check: Ensure the user owns the task
        if($task->user_id !== Auth::id()) {
            // You must not let users update tasks they dont own!
            abort(403, 'Unauthorized action.');
        }

        // 2. Validate the incoming request data (must be a boolean)
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'due_date' => 'nullable|date',
            'is_completed' => 'sometimes|boolean',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
        ]);

        try {
            if(isset($validated['is_completed']) && $validated['is_completed'] && !$task->is_completed) {
                $validated['completed_at'] = now();
                $validated['status'] = 'completed';
            } elseif (isset($validated['is_completed']) && !$validated['is_completed']) {
                $validated['completed_at'] = null;
            }
            // Update the task status
            $task->update($validated);
    
            Log::info('Task updated', ['task_id' => $task->id, 'user_id' => Auth::id()]);
    
            return back()->with('success', 'Task updated successfully!');
        } catch (\Exception $e) {
            Log::error('Task update failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update task.']);
        }

    }

    public function destroy(Task $task) { 
        if($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $taskTitle = $task->title;
            $task->delete();
            Log::info('Task deleted', ['task_title' => $taskTitle, 'user_id' => Auth::id()]);

            return redirect()->route('dashboard')->with('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Task deletion failed: ' . $e->getMessage());
            return back()->withErrors('error', 'Failed to delete task.');
        }
    }

    public function bulkDelete(Request $request) {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        try {
            $deleted = Auth::user()->tasks()
            ->whereIn('id', $validated['task_ids'])
            ->delete();

            Log::info('Bulk delete', ['count' => $deleted, 'user_id' => Auth::id()]);
            return back()->with('success', "{$deleted} tasks deleted successfully!");
        } catch (\Exception $e) {
            Log::error('Bulk delete failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete tasks.']);
        }
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $tasks = $user->tasks()->get();

        $csvData = "Title,Description,Priority,Status,Due Date,Completed,Created At\n";
        
        foreach ($tasks as $task) {
            $csvData .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                str_replace('"', '""', $task->title),
                str_replace('"', '""', $task->description ?? ''),
                $task->priority,
                $task->status,
                $task->due_date ? $task->due_date->format('Y-m-d') : '',
                $task->is_completed ? 'Yes' : 'No',
                $task->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="tasks-export-' . now()->format('Y-m-d') . '.csv"');
    }

}
