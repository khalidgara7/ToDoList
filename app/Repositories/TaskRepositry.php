<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Get all tasks for a specific user (created by or assigned to)
     *
     * @param User $user
     * @return Collection
     */
    public function getAllForUser(User $user): Collection
    {
        return Task::where('user_id', $user->id)
            ->orWhere('assigned_to', $user->id)
            ->with(['user', 'assignedTo'])
            ->get();
    }

    /**
     * Get tasks created by user
     *
     * @param User $user
     * @return Collection
     */
    public function getCreatedByUser(User $user): Collection
    {
        return $user->tasks()->with(['assignedTo'])->get();
    }

    /**
     * Get tasks assigned to user
     *
     * @param User $user
     * @return Collection
     */
    public function getAssignedToUser(User $user): Collection
    {
        return $user->assignedTasks()->with(['user'])->get();
    }

    /**
     * Find a specific task for a user (created by or assigned to)
     *
     * @param User $user
     * @param int $taskId
     * @return Task|null
     */
    public function findForUser(User $user, int $taskId): ?Task
    {
        return Task::where('id', $taskId)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('assigned_to', $user->id);
            })
            ->with(['user', 'assignedTo'])
            ->first();
    }

    /**
     * Create a new task for a user
     *
     * @param User $user
     * @param array $data
     * @return Task
     */
    public function createForUser(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }

    /**
     * Update a task
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    /**
     * Delete a task
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Check if task belongs to user (created by or assigned to)
     *
     * @param Task $task
     * @param User $user
     * @return bool
     */
    public function belongsToUser(Task $task, User $user): bool
    {
        return $task->user_id === $user->id || $task->assigned_to === $user->id;
    }

    /**
     * Check if user can modify task (only creator can modify)
     *
     * @param Task $task
     * @param User $user
     * @return bool
     */
    public function canModifyTask(Task $task, User $user): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * Find user by ID (for assignment)
     *
     * @param int $userId
     * @return User|null
     */
    public function findUser(int $userId): ?User
    {
        return User::find($userId);
    }
}