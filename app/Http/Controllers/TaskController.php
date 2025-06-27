<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Traits\FlashMessages;
use App\Jobs\SendTaskAssignmentNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    use FlashMessages;

    /**
     * Get comments for a task with pagination
     */
    public function getComments(Request $request, Task $task): JsonResponse
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'offset' => 'integer|min:0',
                'limit' => 'integer|min:1|max:10'
            ]);

            $offset = $validated['offset'] ?? 0;
            $limit = $validated['limit'] ?? 5;

            // Get comments with creator information
            $comments = $task->taskComments()
                ->with('creator:id,name')
                ->orderBy('created_at', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            // Get total comments count
            $totalComments = $task->taskComments()->count();

            // Render the comments HTML server-side
            $commentsHtml = view('components.partials.comment-list', [
                'comments' => $comments
            ])->render();

            // Calculate remaining comments and button text
            $newOffset = $offset + $comments->count();
            $remainingComments = $totalComments - $newOffset;

            $buttonText = '';
            if ($remainingComments > 0) {
                if ($remainingComments <= 5) {
                    $buttonText = "View {$remainingComments} more comment" . ($remainingComments > 1 ? 's' : '');
                } else {
                    $buttonText = 'Load more comments';
                }
            }

            return response()->json([
                'success' => true,
                'comments_html' => $commentsHtml,
                'total_comments' => $totalComments,
                'new_offset' => $newOffset,
                'remaining_comments' => $remainingComments,
                'button_text' => $buttonText,
                'has_more' => $remainingComments > 0
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load comments.',
                'error' => 'An error occurred while loading comments.'
            ], 500);
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Request $request, Task $task): JsonResponse|RedirectResponse
    {
        try {
            // Ensure task exists and has a project
            if (!$task->project) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Task not found or invalid.',
                        'error' => 'Task does not belong to any project'
                    ], 404);
                }

                $this->flashError('Task not found or invalid.');
                return redirect()->back();
            }

            $this->authorize('delete', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this task.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            $this->flashError('You do not have permission to delete this task.');
            return redirect()->back();
        }

        try {
            $projectId = $task->project_id;
            $taskTitle = $task->title;

            // Delete the task (this will also cascade delete related comments due to foreign key constraints)
            $task->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Task '{$taskTitle}' has been deleted successfully!",
                    'redirect_url' => route('projects.show', $projectId)
                ]);
            }

            $this->flashSuccess("Task '{$taskTitle}' has been deleted successfully!");
            return redirect()->route('projects.show', $projectId);

        } catch (\Exception $e) {
            Log::error('Task deletion failed', [
                'task_id' => $task->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete task. Please try again.',
                    'error' => 'An error occurred while deleting the task.'
                ], 500);
            }

            $this->flashError('Failed to delete task. Please try again.');
            return redirect()->back();
        }
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            // Validate the basic request data first
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'priority' => 'required|in:low,medium,high',
                'due_date' => 'nullable|date|after:today',
                'assigned_to' => 'nullable|exists:users,id'
            ]);

            // Get the project to check authorization
            $project = Project::findOrFail($validated['project_id']);

            // Check if user can create tasks in this project
            $this->authorize('createInProject', [Task::class, $project]);

            // If assigned_to is provided, validate that the user is a project member and not an admin
            if (!empty($validated['assigned_to'])) {
                // Check if the assigned user is an admin
                $assignedUser = \App\Models\User::find($validated['assigned_to']);
                if ($assignedUser && $assignedUser->role === 'admin') {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Admins cannot be assigned to tasks.',
                            'errors' => ['assigned_to' => ['Admins cannot be assigned to tasks.']]
                        ], 422);
                    }

                    $this->flashError('Admins cannot be assigned to tasks.');
                    return redirect()->back()->withInput();
                }

                $assigneeIsProjectMember = $project->users()
                    ->where('user_id', $validated['assigned_to'])
                    ->exists();

                $assigneeIsProjectCreator = $project->created_by == $validated['assigned_to'];

                if (!$assigneeIsProjectMember && !$assigneeIsProjectCreator) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Task can only be assigned to project members.',
                            'errors' => ['assigned_to' => ['Selected user is not a member of this project.']]
                        ], 422);
                    }

                    $this->flashError('Task can only be assigned to project members.');
                    return redirect()->back()->withInput();
                }
            }

            // Create the task
            $task = Task::create([
                'project_id' => $validated['project_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'status' => 'pending', // Default status
                'due_date' => $validated['due_date'] ?? null,
                'assigned_to' => $validated['assigned_to'] ?? null,
                'created_by' => $request->user()->id
            ]);

            // Load relationships for response
            $task->load(['assignedUser:id,name,email', 'creator:id,name,email', 'project:id,name']);

            // Dispatch notification job if task is assigned to someone
            if ($task->assigned_to && $task->assignedUser) {
                SendTaskAssignmentNotification::dispatch(
                    $task,
                    $task->assignedUser,
                    $request->user()
                );
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task created successfully!',
                    'task' => $task,
                    'redirect_url' => route('projects.show', $project->id)
                ], 201);
            }

            $this->flashSuccess('Task created successfully!');
            return redirect()->route('projects.show', $project->id);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to create tasks in this project.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            $this->flashError('You do not have permission to create tasks in this project.');
            return redirect()->back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Task creation failed', [
                'user_id' => $request->user()->id,
                'project_id' => $request->get('project_id'),
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create task. Please try again.',
                    'error' => 'An error occurred while creating the task.'
                ], 500);
            }

            $this->flashError('Failed to create task. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Request $request, Task $task): JsonResponse
    {
        try {
            $this->authorize('update', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this task.',
                'error' => 'Unauthorized access attempt'
            ], 403);
        }

        try {
            // Load task with relationships
            $task->load(['project.creator', 'assignedUser', 'creator']);

            // Get project members (excluding admins) for reassignment
            $projectMembers = $task->project->users()
                ->where('role', '!=', 'admin')
                ->select('users.id', 'users.name', 'users.role')
                ->get();

            // Add project creator if not an admin
            if ($task->project->creator->role !== 'admin') {
                $creatorData = [
                    'id' => $task->project->creator->id,
                    'name' => $task->project->creator->name,
                    'role' => $task->project->creator->role
                ];

                // Check if creator is not already in the members list
                $creatorExists = $projectMembers->contains('id', $task->project->creator->id);
                if (!$creatorExists) {
                    $projectMembers->push((object) $creatorData);
                }
            }

            return response()->json([
                'success' => true,
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                    'assigned_to' => $task->assigned_to,
                    'project_id' => $task->project_id
                ],
                'project_members' => $projectMembers->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Task edit failed', [
                'task_id' => $task->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load task data.',
                'error' => 'An error occurred while loading the task.'
            ], 500);
        }
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        try {
            $this->authorize('update', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this task.',
                'error' => 'Unauthorized access attempt'
            ], 403);
        }

        try {
            // Validate the request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
                'due_date' => 'nullable|date|after:today',
                'assigned_to' => 'nullable|exists:users,id'
            ]);

            // If assigned_to is provided, validate that the user is a project member and not an admin
            if (!empty($validated['assigned_to'])) {
                // Check if the assigned user is an admin
                $assignedUser = \App\Models\User::find($validated['assigned_to']);
                if ($assignedUser && $assignedUser->role === 'admin') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admins cannot be assigned to tasks.',
                        'errors' => ['assigned_to' => ['Admins cannot be assigned to tasks.']]
                    ], 422);
                }

                $assigneeIsProjectMember = $task->project->users()
                    ->where('user_id', $validated['assigned_to'])
                    ->exists();

                $assigneeIsProjectCreator = $task->project->created_by == $validated['assigned_to'];

                if (!$assigneeIsProjectMember && !$assigneeIsProjectCreator) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Task can only be assigned to project members.',
                        'errors' => ['assigned_to' => ['Selected user is not a member of this project.']]
                    ], 422);
                }
            }

            // Store the original assigned_to value to check if it changed
            $originalAssignedTo = $task->assigned_to;

            // Update the task
            $task->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'status' => $validated['status'],
                'due_date' => $validated['due_date'] ?? null,
                'assigned_to' => $validated['assigned_to'] ?? null,
            ]);

            // Load relationships for response
            $task->load(['assignedUser:id,name,email', 'creator:id,name,email', 'project:id,name']);

            // Dispatch notification job if task assignment changed and is now assigned to someone
            if ($task->assigned_to && $task->assigned_to !== $originalAssignedTo && $task->assignedUser) {
                SendTaskAssignmentNotification::dispatch(
                    $task,
                    $task->assignedUser,
                    $request->user()
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task,
                'redirect_url' => route('projects.show', $task->project_id)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Task update failed', [
                'task_id' => $task->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update task. Please try again.',
                'error' => 'An error occurred while updating the task.'
            ], 500);
        }
    }

    /**
     * Update task status (for assigned users and admins)
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'status' => 'required|in:in_progress,completed'
            ]);

            $user = $request->user();

            // Check if user can update status (admin or assigned user)
            $canUpdateStatus = $user->role === 'admin' || ($task->assigned_to && $task->assigned_to == $user->id);

            if (!$canUpdateStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this task status.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            // Validate status transition
            $currentStatus = $task->status;
            $newStatus = $validated['status'];

            // Only allow specific transitions
            $validTransitions = [
                'pending' => ['in_progress'],
                'in_progress' => ['completed']
            ];

            if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'.",
                    'error' => 'Invalid status transition'
                ], 422);
            }

            // Update the task status
            $task->update(['status' => $newStatus]);

            // Load relationships for response
            $task->load(['assignedUser:id,name,email', 'creator:id,name,email', 'project:id,name']);

            $statusText = $newStatus === 'in_progress' ? 'started' : 'completed';

            return response()->json([
                'success' => true,
                'message' => "Task has been {$statusText} successfully!",
                'task' => $task,
                'redirect_url' => route('projects.show', $task->project_id)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Task status update failed', [
                'task_id' => $task->id,
                'user_id' => $request->user()->id,
                'requested_status' => $request->get('status'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status. Please try again.',
                'error' => 'An error occurred while updating the task status.'
            ], 500);
        }
    }

    /**
     * Store a new task comment.
     */
    public function storeComment(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'comment' => 'required|string|max:500|min:1'
            ]);

            // Find the task
            $task = Task::findOrFail($validated['task_id']);

                        // Check if user can comment on this task (must be project member or admin)
            $user = $request->user();

            if (!($user->role === 'admin' || $task->project->isMember($user))) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to comment on this task.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            // Create the comment
            $comment = \App\Models\TaskComment::create([
                'task_id' => $task->id,
                'created_by' => $user->id,
                'comment' => trim($validated['comment'])
            ]);

            // Load the creator relationship
            $comment->load('creator:id,name,email');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully!',
                'comment' => $comment
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Task comment creation failed', [
                'user_id' => $request->user()->id,
                'task_id' => $request->get('task_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment. Please try again.',
                'error' => 'An error occurred while adding the comment.'
            ], 500);
        }
    }
}
