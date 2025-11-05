<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Task;

interface NotificationServiceInterface
{
    /**
     * Send task creation notification
     *
     * @param Task $task
     * @param User $assignedUser
     * @return void
     */
    public function sendTaskCreatedNotification(Task $task, User $assignedUser = null): void;

    /**
     * Send task assignment notification
     *
     * @param Task $task
     * @param User $assignedUser
     * @return void
     */
    public function sendTaskAssignedNotification(Task $task, User $assignedUser): void;

    /**
     * Get user notifications
     *
     * @param User $user
     * @param bool $unreadOnly
     * @return array
     */
    public function getUserNotifications(User $user, bool $unreadOnly = false): array;

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @param User $user
     * @return array
     */
    public function markAsRead(int $notificationId, User $user): array;

    /**
     * Mark all notifications as read
     *
     * @param User $user
     * @return array
     */
    public function markAllAsRead(User $user): array;
}