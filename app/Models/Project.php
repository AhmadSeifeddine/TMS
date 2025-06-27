<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Helper methods for policy integration

    /**
     * Scope projects that the user can view based on their role
     */
    public function scopeViewableBy($query, User $user)
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

    /**
     * Check if user is the creator of this project
     */
    public function isCreatedBy(User $user): bool
    {
        return $this->created_by === $user->id;
    }

    /**
     * Check if user is assigned to this project
     */
    public function hasUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Get available assignees for this project (users assigned to the project)
     */
    public function getAvailableAssignees()
    {
        return $this->users()->where('role', '!=', 'admin')->get();
    }

    /**
     * Check if user is a member of this project (either creator or assigned member)
     */
    public function isMember(User $user): bool
    {
        return $this->isCreatedBy($user) || $this->hasUser($user);
    }
}
