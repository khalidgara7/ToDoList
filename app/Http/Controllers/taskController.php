<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class taskController extends Controller
{
    // Get all tasks for the authenticated user
    public function index()
    {
       $user = Auth::user();
       $tasks = $user->tasks;

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    // Get a specific task for the authenticated user
    public function show($id)
    {
        $user = Auth::user();
        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    // Create a new task for the authenticated user
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed'
        ]);

        $user = Auth::user();
        
        $task = $user->tasks()->create([
            'title' => $request->title,
            'description' => $request->description ?? '',
            'status' => $request->status ?? 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    // Update a specific task for the authenticated user
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed'
        ]);

        $user = Auth::user();
        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->update([
            'title' => $request->title,
            'description' => $request->description ?? $task->description,
            'status' => $request->status ?? $task->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    // Delete a specific task for the authenticated user
    public function destroy($id)
    {
        $user = Auth::user();
        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }
}
