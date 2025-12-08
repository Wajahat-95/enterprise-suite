<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class TaskController extends Controller
{
    // Method 1: Fetch all tasks from the database and return them as JSON
    public function index() {
        // 1. Get the authenticated user object
        $user = Auth::user();

        // 2. Use the relationship to getch only their tasks
        $tasks = $user->tasks()->get();

        return response()->json($tasks);
    }

    public function store(Request $request) {
        // 1. Validate the incoming request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // 2. Create the task, automatically linking it to the user
        Auth::user()->tasks()->create($validated);

        // 3. Redirect back to the tasks list
        return redirect(route('dashboard'));
    }

    public function dashboard(Request $request) {
        $user = Auth::user();

        // 1. Start the Base Query (secured to the user)
        $query = $user->tasks()->latest();

        // 2. Add Filtering Logic based on 'status' query parameter
        if($request->has('status')) {
            if($request->status === 'completed') {
                $query->where('is_completed', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_completed', false);
            }
        }

        // 3. ADD SEARCH LOGIC based on 'search' query parameter
        if($request->has('search') && $request->search !== null) {
            $searchTerm = '%' . $request->search . '%';
            // We use 'where' and 'like' to find partial matches in the title
            $query->where('title', 'like', $searchTerm); 
        }

        // 4. Apply Pagination to the final query
        // We also pass the search term back to frontend to keep the input populated
        $tasks = $query->paginate(10)->withQueryString();




        // // 1. Use paginate(10) instead of get() to fetch chunks of 10 tasks.
        // // 2. Use latest() to ensure the newest tasks appear first.
        // $tasks = $user->tasks()->latest()->paginate(10);

        return Inertia::render('Dashboard', [
            // The $tasks variable now contains all pagination meta-data (links, counts, etc.)

            'tasks' => $tasks
        ]);
    }

    public function destroy(Task $task) { // Laravel automatically fetches the Task Object based on the ID
        // Security Check: Ensure the user owns the task before deleting
        if($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);   }

        try {
            $task->delete();
            return redirect()->route('dashboard')->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Task deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete task');
        }
    }

    public function update(Request $request, Task $task) {
        // 1. Security Check: Ensure the user owns the task
        if($task->user_id !== Auth::id()) {
            // You must not let users update tasks they dont own!
            abort(403, 'Unauthorized action.');
        }

        // 2. Validate the incoming request data (must be a boolean)
        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        // 3. Update the task status
        $task->update($validated);

        // 4. Since this is an AJAX/Inertia request, redirect back to the current page.
        return redirect()->back();
    }
}
