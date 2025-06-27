<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view the projects index
        return true;
    }

    /**
     * Determine whether the user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        // All authenticated users can view project details (for the modal)
        // This allows everyone to see basic project information
        return true;
    }

    /**
     * Determine whether the user can create projects.
     */
    public function create(User $user): bool
    {
        // Only admins and creators can create projects
        return in_array($user->role, ['admin', 'creator']);
    }

    /**
     * Determine whether the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        // Admins can update any project, creators can only update their own projects
        return $user->role === 'admin' || $user->id === $project->created_by;
    }

    /**
     * Determine whether the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        // Admins can delete any project, creators can only delete their own projects
        return $user->role === 'admin' || $user->id === $project->created_by;
    }

    /**
     * Determine whether the user can restore the project.
     */
    public function restore(User $user, Project $project): bool
    {
        // Only the project creator can restore the project
        return $user->id === $project->created_by;
    }

    /**
     * Determine whether the user can permanently delete the project.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        // Only admins can force delete projects
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can manage project members (assign/remove assignees).
     */
    public function manageMembers(User $user, Project $project): bool
    {
        // Admins can manage any project members, creators can only manage their own projects
        return $user->role === 'admin' || $user->id === $project->created_by;
    }

    /**
     * Determine whether the user can create tasks in the project.
     */
    public function createTasks(User $user, Project $project): bool
    {
        // Admins can create tasks in any project, creators can only create tasks in their own projects
        return $user->role === 'admin' || $user->id === $project->created_by;
    }

    /**
     * Determine whether the user can view project statistics.
     */
    public function viewStatistics(User $user, Project $project): bool
    {
        // Users can view statistics if they can view the project
        return $this->view($user, $project);
    }

    /**
     * Get projects that the user can view based on their role.
     */
    public static function scopeViewable($query, User $user)
    {
        if ($user->role === 'admin') {
            // Admins can see all projects
            return $query;
        } elseif ($user->role === 'creator') {
            // Creators can see their own projects + projects they're assigned to
            return $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('users', function ($subQuery) use ($user) {
                      $subQuery->where('user_id', $user->id);
                  });
            });
        } else {
            // Assignees and members can only see projects they're assigned to
            return $query->whereHas('users', function ($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id);
            });
        }
    }
}
