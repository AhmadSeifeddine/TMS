<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;

class TaskCommentPolicy
{
    /**
     * Determine whether the user can view any task comments.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view comments
        return true;
    }

    /**
     * Determine whether the user can view the task comment.
     */
    public function view(User $user, TaskComment $taskComment): bool
    {
        // Users can view comments if they can view the task
        return $user->can('view', $taskComment->task);
    }

    /**
     * Determine whether the user can create task comments.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create comments (if they can view the task)
        return true;
    }

    /**
     * Determine whether the user can create comments on a specific task.
     */
    public function createOnTask(User $user, $task): bool
    {
        // Users can add comments if they can view the task
        return $user->can('view', $task);
    }

    /**
     * Determine whether the user can update the task comment.
     */
    public function update(User $user, TaskComment $taskComment): bool
    {
        // Users can only update their own comments
        return $user->id === $taskComment->created_by;
    }

    /**
     * Determine whether the user can delete the task comment.
     */
    public function delete(User $user, TaskComment $taskComment): bool
    {
        // Users can delete their own comments OR project creators can delete any comment on their project tasks
        return $user->id === $taskComment->created_by ||
            $user->id === $taskComment->task->project->created_by;
    }

    /**
     * Determine whether the user can restore the task comment.
     */
    public function restore(User $user, TaskComment $taskComment): bool
    {
        // Users can restore their own comments OR project creators can restore any comment on their project tasks
        return $user->id === $taskComment->created_by ||
            $user->id === $taskComment->task->project->created_by;
    }

    /**
     * Determine whether the user can permanently delete the task comment.
     */
    public function forceDelete(User $user, TaskComment $taskComment): bool
    {
        // Only admins can force delete comments
        return $user->role === 'admin';
    }
}
