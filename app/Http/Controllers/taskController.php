<?php

namespace App\Http\Controllers;

use App\Services\TaskServiceInterface;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class taskController extends Controller
{
    protected TaskServiceInterface $taskService;

    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
        $this->middleware('auth:api');
    }

    /**
     * Get all tasks for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $result = $this->taskService->getAllTasks($user);

        return response()->json($result);
    }

    /**
     * Get a specific task for the authenticated user
     */
    public function show($id)
    {
        $user = Auth::user();
        $result = $this->taskService->getTask($user, $id);

        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Create a new task for the authenticated user
     */
    public function store(StoreTaskRequest $request)
    {
        $user = Auth::user();
        $result = $this->taskService->createTask($user, $request->validated());

        return response()->json($result, 201);
    }

    /**
     * Update a specific task for the authenticated user
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        $user = Auth::user();
        $result = $this->taskService->updateTask($user, $id, $request->validated());

        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Delete a specific task for the authenticated user
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $result = $this->taskService->deleteTask($user, $id);

        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }
}