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

    // Securely create a test task for the logged-in user

    // public function createTestTask() {
    //     // Get the ID of the currently logged-in user
    //     $userId = Auth::id();

    //     // Use the ID when creating the task
    //     $task = Task::create([
    //         'user_id' => $userId, // This is now dynamic
    //         'title' => 'Complete Project Two: Task CRUD',
    //         'is_completed' => false
    //     ]);

    //     return response('Test Task Created for User ID: ' . $userId, 200);
    // }

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

    public function dashboard() {
        $user = Auth::user();

        // Pass the user's tasks directly to the Dashboard.vue component
        return Inertia::render('Dashboard', [
            'tasks' => $user->tasks()->get()
        ]);
    }

    public function destroy(Task $task) { // Laravel automatically fetches the Task Object based on the ID
        // Security Check: Ensure the user owns the task before deleting
        if($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.'); // Stop if the user is not the owner
        }

        $task->delete();

        // Redirect bac to te dashboard to instantly update the list
        return redirect(route('dashboard'));
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
