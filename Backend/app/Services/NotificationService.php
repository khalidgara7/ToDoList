<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Task;

class NotificationService implements NotificationServiceInterface
{
    /**
     * Send task creation notification
     *
     * @param Task $task
     * @param User $assignedUser
     * @return void
     */
    public function sendTaskCreatedNotification(Task $task, User $assignedUser = null): void
    {
        // Notify the creator
        $this->createNotification(
            $task->user,
            'task_created',
            'Task Created Successfully',
            "Your task '{$task->title}' has been created successfully.",
            ['task_id' => $task->id, 'task_title' => $task->title]
        );

        // If task is assigned to someone else, notify them too
        if ($assignedUser && $assignedUser->id !== $task->user_id) {
            $this->sendTaskAssignedNotification($task, $assignedUser);
        }
    }

    /**
     * Send task assignment notification
     *
     * @param Task $task
     * @param User $assignedUser
     * @return void
     */
    public function sendTaskAssignedNotification(Task $task, User $assignedUser): void
    {
        $this->createNotification(
            $assignedUser,
            'task_assigned',
            'New Task Assigned',
            "You have been assigned a new task: '{$task->title}' by {$task->user->full_name}.",
            [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'assigned_by' => $task->user->full_name
            ]
        );
    }

    /**
     * Get user notifications
     *
     * @param User $user
     * @param bool $unreadOnly
     * @return array
     */
    public function getUserNotifications(User $user, bool $unreadOnly = false): array
    {
        $query = $user->notifications()->orderBy('created_at', 'desc');
        
        if ($unreadOnly) {
            $query->unread();
        }

        $notifications = $query->paginate(20);

        return [
            'success' => true,
            'data' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
            'unread_count' => $user->unreadNotifications()->count()
        ];
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @param User $user
     * @return array
     */
    public function markAsRead(int $notificationId, User $user): array
    {
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return [
                'success' => false,
                'message' => 'Notification not found'
            ];
        }

        $notification->markAsRead();

        return [
            'success' => true,
            'message' => 'Notification marked as read'
        ];
    }

    /**
     * Mark all notifications as read
     *
     * @param User $user
     * @return array
     */
    public function markAllAsRead(User $user): array
    {
        $user->unreadNotifications()->update(['read_at' => now()]);

        return [
            'success' => true,
            'message' => 'All notifications marked as read'
        ];
    }

    /**
     * Create a notification
     *
     * @param User $user
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @return Notification
     */
    private function createNotification(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}