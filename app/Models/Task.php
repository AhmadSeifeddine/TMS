<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'project_id',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'status' => 'string',
        'priority' => 'string',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function taskComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    // Helper methods for policy integration

    /**
     * Scope tasks that the user can view based on their permissions
     */
    public function scopeViewableBy($query, User $user)
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

    /**
     * Check if user can update this task status
     */
    public function canUpdateStatusBy(User $user): bool
    {
        return $user->id === $this->project->created_by ||
            $user->id === $this->assigned_to;
    }

    /**
     * Check if user is assigned to this task
     */
    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    /**
     * Check if user created the project this task belongs to
     */
    public function isProjectOwnedBy(User $user): bool
    {
        return $this->project->created_by === $user->id;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityBadgeColor(): string
    {
        return match ($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
