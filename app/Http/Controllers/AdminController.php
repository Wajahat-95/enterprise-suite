<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard() {
        // Overall Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('is_completed', true)->count(),
            'tasks_today' => Task::whereDate('created_at', today())->count(),
            'users_today' => User::whereDate('created_at', today())->count(),
        ];

        // Recent Users
        $recentUsers = User::latest()
            ->take(5)->get(['id', 'name', 'email', 'role', 'is_active', 'created_at']);

        // Recent Activity
        $recentActivity = ActivityLog::with('user:id,name,email')
            ->latest()
            ->take(10)
            ->get();
        
        // Task Statistics by Priority
        $tasksByPriority = Task::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();
        
        // Task Statistics by Status
        $tasksByStatus = Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // User Growth (Last 7 days)
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Task Completion Rate by User (Top 10)
        $topUsers = User::withCount([
            'tasks',
            'tasks as completed_tasks' => function ($query) {
                $query->where('is_completed', true);
            }
        ])
        ->having('tasks_count', '>', 0)
        ->orderByDesc('completed_tasks')
        ->take(10)
        ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentActivity' => $recentActivity,
            'tasksByPriority' => $tasksByPriority,
            'tasksByStatus' => $tasksByStatus,
            'userGrowth' => $userGrowth,
            'topUsers' => $topUsers,
        ]);
    }

    // Display list of all users

    public function users(Request $request) {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if(in_array($sortField, ['name', 'email', 'created_at', 'last_login_at'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $users = $query->withCount('tasks')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status', 'sort', 'direction']),
        ]);
    }

    // Update user information
    public function updateUser(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', Rule::in(['user', 'admin'])],
            'is_active' => 'sometimes|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if(isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => "Updated user: {$user->email}",
            'properties' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', '👤 User updated successfully!');
    }

    // Delete a user
    public function deleteUser(User $user) {
        // prevent self-deletion
        if($user->id === auth()->id()) {
            return back()->with('error', '⚠️ You cannot delete your own account.');
        }

        $email = $user->email;
        $user->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => "Deleted user: {$email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success','🗑️ User deleted successfully!');
    }

    public function allTasks(Request $request) {
        $query = Task::with('user:id,name,email');

        // Search
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Filter by priority
        if($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if($request->filled('status')) {
            if($request->status === 'completed') {
                $query->where('is_completed', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_completed', false);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if(in_array($sortField, ['title', 'priority', 'due_date', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $tasks = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Tasks', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'priority', 'status', 'sort', 'direction']),
        ]);


    }

    // Display activity logs
    public function activityLogs(Request $request) {
        $query = ActivityLog::with('user:id,name,email');

        // Filter by action
        if($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range
        if($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);

        }
        if($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()
            ->paginate(20)
            ->withQueryString();

        $users = User::select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Activity/Logs', [
            'logs' => $logs,
            'users' => $users,
            'filters' => $request->only(['action', 'user_id', 'date_from', 'date_to']),
        ]);
    }

    // Display system information
    public function systemInfo() {
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug'),
            'environment' => config('app.env'),

        ];

        return Inertia::render('Admin/SystemInfo', [
            'info' => $info,
        ]);
    }
}
