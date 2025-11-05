<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TaskServiceInterface
{
    /**
     * Get all tasks for authenticated user
     *
     * @param User $user
     * @return array
     */
    public function getAllTasks(User $user): array;

    /**
     * Get a specific task for authenticated user
     *
     * @param User $user
     * @param int $taskId
     * @return array
     */
    public function getTask(User $user, int $taskId): array;

    /**
     * Create a new task for authenticated user
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function createTask(User $user, array $data): array;

    /**
     * Update a task for authenticated user
     *
     * @param User $user
     * @param int $taskId
     * @param array $data
     * @return array
     */
    public function updateTask(User $user, int $taskId, array $data): array;

    /**
     * Delete a task for authenticated user
     *
     * @param User $user
     * @param int $taskId
     * @return array
     */
    public function deleteTask(User $user, int $taskId): array;
}