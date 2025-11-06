<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use App\Services\NotificationServiceInterface;
use App\Models\User;

class TaskService implements TaskServiceInterface
{
    protected TaskRepository $taskRepository;
    protected NotificationServiceInterface $notificationService;

    public function __construct(TaskRepository $taskRepository, NotificationServiceInterface $notificationService)
    {
        $this->taskRepository = $taskRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * Get all tasks for authenticated user
     *
     * @param User $user
     * @return array
     */
    public function getAllTasks(User $user): array
    {
        $tasks = $this->taskRepository->getAllForUser($user);

        return [
            'success' => true,
            'data' => $tasks
        ];
    }

    /**
     * Get a specific task for authenticated user
     *
     * @param User $user
     * @param int $taskId
     * @return array
     */
    public function getTask(User $user, int $taskId): array
    {
        $task = $this->taskRepository->findForUser($user, $taskId);

        if (!$task) {
            return [
                'success' => false,
                'message' => 'Task not found'
            ];
        }

        return [
            'success' => true,
            'data' => $task
        ];
    }

    /**
     * Create a new task for authenticated user
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function createTask(User $user, array $data): array
    {
        // Set default values
        $data['description'] = $data['description'] ?? '';
        $data['status'] = $data['status'] ?? 'pending';

        // Validate assigned user if provided
        $assignedUser = null;
        if (isset($data['assigned_to']) && $data['assigned_to']) {
            $assignedUser = $this->taskRepository->findUser($data['assigned_to']);
            if (!$assignedUser) {
                return [
                    'success' => false,
                    'message' => 'Assigned user not found'
                ];
            }
        }

        $task = $this->taskRepository->createForUser($user, $data);
        
        // Load relationships
        $task->load(['user', 'assignedTo']);

        // Send notifications
        $this->notificationService->sendTaskCreatedNotification($task, $assignedUser);

        return [
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ];
    }

    /**
     * Update a task for authenticated user
     *
     * @param User $user
     * @param int $taskId
     * @param array $data
     * @return array
     */
    public function updateTask(User $user, int $taskId, array $data): array
    {
        $task = $this->taskRepository->findForUser($user, $taskId);

        if (!$task) {
            return [
                'success' => false,
                'message' => 'Task not found'
            ];
        }

        // Only task creator can modify the task
        if (!$this->taskRepository->canModifyTask($task, $user)) {
            return [
                'success' => false,
                'message' => 'You can only modify tasks that you created'
            ];
        }

        // Only update fields that are provided
        $updateData = [];
        if (isset($data['title'])) {
            $updateData['title'] = $data['title'];
        }
        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }
        
        // Handle assignment change
        if (isset($data['assigned_to'])) {
            $oldAssignedTo = $task->assigned_to;
            
            if ($data['assigned_to']) {
                $assignedUser = $this->taskRepository->findUser($data['assigned_to']);
                if (!$assignedUser) {
                    return [
                        'success' => false,
                        'message' => 'Assigned user not found'
                    ];
                }
                $updateData['assigned_to'] = $data['assigned_to'];
                
                // Send notification if assignment changed
                if ($oldAssignedTo !== $data['assigned_to']) {
                    $this->notificationService->sendTaskAssignedNotification($task, $assignedUser);
                }
            } else {
                $updateData['assigned_to'] = null;
            }
        }

        $updatedTask = $this->taskRepository->update($task, $updateData);

        return [
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $updatedTask
        ];
    }

    /**
     * Delete a task for authenticated user
     *
     * @param User $user
     * @param int $taskId
     * @return array
     */
    public function deleteTask(User $user, int $taskId): array
    {
        $task = $this->taskRepository->findForUser($user, $taskId);

        if (!$task) {
            return [
                'success' => false,
                'message' => 'Task not found'
            ];
        }

        // Only task creator can delete the task
        if (!$this->taskRepository->canModifyTask($task, $user)) {
            return [
                'success' => false,
                'message' => 'You can only delete tasks that you created'
            ];
        }

        $this->taskRepository->delete($task);

        return [
            'success' => true,
            'message' => 'Task deleted successfully'
        ];
    }
}