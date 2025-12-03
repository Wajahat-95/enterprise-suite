<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Method 1: Fetch all tasks from the database and return them as JSON
    public function index() {
        // "Select * from tasks"
        return Task::all();
    }

    public function createTestTask() {
        $task = Task::create([
            'user_id' => 1, // We are assigning this to the user we just seeded
            'title' => 'Complete the Enterprise Suite setup',
            'is_completed' => false
        ]);

        return "Task Created:" . $task->title;
    }
}
