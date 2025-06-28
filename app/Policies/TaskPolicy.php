<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view the tasks index
        return true;
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Users can view tasks if they can view the project the task belongs to
        return $user->can('view', $task->project);
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        // Only admins and creators can create tasks (but they need to own the project)
        return in_array($user->role, ['admin', 'creator']);
    }

    /**
     * Determine whether the user can create tasks in a specific project.
     */
    public function createInProject(User $user, $project): bool
    {
        // Admins can create tasks in any project, project creators can only create tasks in their own projects
        return $user->role === 'admin' || $user->id === $project->created_by;
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Admins can update any task, project creators can only update tasks in their projects
        return $user->role === 'admin' || $user->id === $task->project->created_by;
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Admins can delete any task, project creators can delete tasks in their projects
        return $user->role === 'admin' || $user->id === $task->project->created_by;
    }

    /**
     * Determine whether the user can restore the task.
     */
    public function restore(User $user, Task $task): bool
    {
        // Only the project creator can restore tasks
        return $user->id === $task->project->created_by;
    }

    /**
     * Determine whether the user can permanently delete the task.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        // Only admins can force delete tasks
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can assign/reassign the task.
     */
    public function assign(User $user, Task $task): bool
    {
        // Admins can assign/reassign any task, project creators can only assign/reassign tasks in their projects
        return $user->role === 'admin' || $user->id === $task->project->created_by;
    }

    /**
     * Determine whether the user can update task status.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        // Users can update status if they:
        // 1. Created the project (project owner) - full control
        // 2. Are assigned to the task - can update status only
        return $user->id === $task->project->created_by ||
               $user->id === $task->assigned_to;
    }

    /**
     * Determine whether the user can update task priority.
     */
    public function updatePriority(User $user, Task $task): bool
    {
        // Admins can update any task priority, project creators can only update priority in their projects
        return $user->role === 'admin' || $user->id === $task->project->created_by;
    }

    /**
     * Determine whether the user can update task due date.
     */
    public function updateDueDate(User $user, Task $task): bool
    {
        // Admins can update any task due date, project creators can only update due dates in their projects
        return $user->role === 'admin' || $user->id === $task->project->created_by;
    }

    /**
     * Determine whether the user can update task details (title, description).
     */
    public function updateDetails(User $user, Task $task): bool
    {
        // Admins can update any task details, project creators can only update details in their projects
        return $user->role === 'admin' || $user->id === $task->project->created_by;
    }

    /**
     * Get tasks that the user can view based on their permissions.
     */
    public static function scopeViewable($query, User $user)
    {
        if ($user->role === 'admin') {
            // Admins can see all tasks
            return $query;
        } else {
            // Users can only see tasks from projects they have access to
            return $query->whereHas('project', function ($projectQuery) use ($user) {
                if ($user->role === 'creator') {
                    // Creators can see tasks from their own projects + projects they're assigned to
                    $projectQuery->where(function ($q) use ($user) {
                        $q->where('created_by', $user->id)
                            ->orWhereHas('users', function ($subQuery) use ($user) {
                            $subQuery->where('user_id', $user->id);
                        });
                    });
                } else {
                    // Assignees and members can only see tasks from projects they're assigned to
                    $projectQuery->whereHas('users', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                }
            });
        }
    }
}
