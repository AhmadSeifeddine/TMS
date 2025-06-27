<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Traits\FlashMessages;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    use FlashMessages;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $user = $request->user();
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'newest');

        // Create cache key for this specific query
        $cacheKey = "projects_index_{$user->id}_{$search}_{$sort}";

        // Try to get from cache first (cache for 5 minutes)
        $cachedData = $this->getProjectsData($user, $search ?? '', $sort);

        // Handle AJAX requests for search/filter
        if ($request->expectsJson()) {
            // Render the appropriate view based on user role
            $viewName = match($user->role) {
                'member' => 'dashboard.projects.partials.member-view',
                'assignee' => 'dashboard.projects.partials.assignee-view',
                'creator' => 'dashboard.projects.partials.creator-view',
                'admin' => 'dashboard.projects.partials.admin-view',
                default => 'dashboard.projects.partials.member-view'
            };

            $html = view($viewName, [
                'organizedProjects' => $cachedData['organizedProjects'],
                'user' => $user
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'organizedProjects' => $cachedData['organizedProjects'],
                'totalProjects' => $cachedData['totalProjects'],
                'search' => $search,
                'sort' => $sort
            ]);
        }

        return view('dashboard.projects.index', [
            'user' => $user,
            'organizedProjects' => $cachedData['organizedProjects'],
            'totalProjects' => $cachedData['totalProjects'],
            'search' => $search,
            'sort' => $sort,
            'hasFilters' => !empty($search) || $sort !== 'newest',
        ]);
    }

    /**
     * Get projects data with optimized queries - show all projects but access control applied on click
     */
    private function getProjectsData(User $user, string $search, string $sort): array
    {
        // Build optimized query with eager loading to prevent N+1 queries
        $query = Project::with([
            'creator:id,name,email,role',
            'creator.media',
            'users:id,name,email,role',
            'users.media',
            'tasks:id,project_id,status',
        ]);

        // Apply search filter with optimized query
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply sorting with optimized indexes
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $projects = $query->get();

        // Optimize the organization method to avoid N+1 queries
        $organizedProjects = $this->organizeProjectsByUserRelationshipOptimized($user, $projects);

        return [
            'organizedProjects' => $organizedProjects,
            'totalProjects' => $projects->count()
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        // TODO: Implement create form
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Project::class);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to create projects.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            $this->flashError('You do not have permission to create projects.');
            return redirect()->route('projects.index');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:active,completed,archived'
        ]);

        try {
            // Create the project
            $project = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'status' => $validated['status'],
                'created_by' => $request->user()->id,
            ]);

            // Clear relevant caches
            $this->clearProjectCaches($request->user());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project created successfully!',
                    'project' => $project->load(['creator', 'creator.media', 'users', 'users.media', 'tasks'])
                ]);
            }

            $this->flashSuccess('Project created successfully!');
            return redirect()->route('projects.index');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create project.',
                    'errors' => ['general' => ['An error occurred while creating the project.']]
                ], 500);
            }

            $this->flashError('Failed to create project. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Project $project)
    {
        try {
            $this->authorize('view', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this project.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            $this->flashError('You do not have permission to view this project.');
            return redirect()->route('projects.index');
        }

        // Get the status filter from request
        $statusFilter = $request->get('status', 'all');

        // Load project with basic relationships
        $project->load([
            'creator:id,name,email,role',
            'creator.media',
            'users:id,name,email,role',
            'users.media'
        ]);

        // Build task query based on status filter
        $tasksQuery = $project->tasks()->with([
            'assignedUser:id,name,email,role',
            'assignedUser.media',
            'creator:id,name,email,role',
            'creator.media',
            'taskComments' => function ($commentQuery) {
                $commentQuery->with([
                    'creator:id,name,email,role',
                    'creator.media'
                ])
                    ->latest()
                    ->take(5); // Load latest 5 comments per task
            }
        ]);

        // Apply status filter if not 'all'
        if ($statusFilter !== 'all') {
            $tasksQuery->where('status', $statusFilter);
        }

        // Get filtered tasks
        $filteredTasks = $tasksQuery->orderBy('created_at', 'desc')->get();

        // Get all tasks for statistics (regardless of filter)
        $allTasks = $project->tasks()->get();

        // Organize all tasks by status for counts
        $tasksByStatus = [
            'all' => $allTasks,
            'pending' => $allTasks->where('status', 'pending'),
            'in_progress' => $allTasks->where('status', 'in_progress'),
            'completed' => $allTasks->where('status', 'completed')
        ];

        // Calculate project statistics
        $totalTasks = $allTasks->count();
        $completedTasks = $allTasks->where('status', 'completed')->count();
        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Get overdue tasks
        $overdueTasks = $allTasks->filter(function ($task) {
            return $task->due_date &&
                \Carbon\Carbon::parse($task->due_date)->isPast() &&
                $task->status !== 'completed';
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'project' => $project,
                'tasks' => $filteredTasks,
                'taskCounts' => [
                    'all' => $tasksByStatus['all']->count(),
                    'pending' => $tasksByStatus['pending']->count(),
                    'in_progress' => $tasksByStatus['in_progress']->count(),
                    'completed' => $tasksByStatus['completed']->count()
                ],
                'statistics' => [
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'progress_percentage' => $progressPercentage,
                    'overdue_tasks' => $overdueTasks->count()
                ],
                'currentFilter' => $statusFilter
            ]);
        }

        return view('dashboard.projects.show', [
            'project' => $project,
            'tasks' => $filteredTasks,
            'taskCounts' => [
                'all' => $tasksByStatus['all']->count(),
                'pending' => $tasksByStatus['pending']->count(),
                'in_progress' => $tasksByStatus['in_progress']->count(),
                'completed' => $tasksByStatus['completed']->count()
            ],
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'progressPercentage' => $progressPercentage,
            'overdueTasks' => $overdueTasks,
            'currentFilter' => $statusFilter,
            'user' => $request->user()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        try {
            $this->authorize('update', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this project.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            $this->flashError('You do not have permission to edit this project.');
            return redirect()->route('projects.index');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'project' => $project
            ]);
        }

        // For non-AJAX requests, redirect to projects index
        return redirect()->route('projects.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        try {
            $this->authorize('update', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this project.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            $this->flashError('You do not have permission to update this project.');
            return redirect()->route('projects.index');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:active,completed,archived'
        ]);

        try {
            $project->update($validated);

            // Clear relevant caches
            $this->clearProjectCaches($request->user());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project updated successfully!',
                    'project' => $project->load(['creator', 'creator.media', 'users', 'users.media', 'tasks'])
                ]);
            }

            $this->flashSuccess('Project updated successfully!');
            return redirect()->route('projects.index');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update project.',
                    'errors' => ['general' => ['An error occurred while updating the project.']]
                ], 500);
            }

            $this->flashError('Failed to update project. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $this->authorize('delete', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this project.',
                    'error' => 'Unauthorized access attempt'
                ], 403);
            }

            $this->flashError('You do not have permission to delete this project.');
            return redirect()->route('projects.index');
        }

        try {
            $projectName = $project->name;
            $project->delete();

            // Clear relevant caches
            $this->clearProjectCaches(request()->user());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Project '{$projectName}' deleted successfully!"
                ]);
            }

            $this->flashSuccess("Project '{$projectName}' deleted successfully!");
            return redirect()->route('projects.index');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete project.',
                    'error' => 'An error occurred while deleting the project.'
                ], 500);
            }

            $this->flashError('Failed to delete project. Please try again.');
            return redirect()->back();
        }
    }

    /**
     * Get team data for managing project members - Optimized
     */
    public function getTeamData(Project $project)
    {
        try {
            $this->authorize('manageMembers', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage this project\'s team.',
                'error' => 'Unauthorized access attempt'
            ], 403);
        }

        try {
            // Cache team data for 5 minutes
            $cacheKey = "project_team_data_{$project->id}";

            $teamData = Cache::remember($cacheKey, 300, function () use ($project) {
                // Get current team members (both creators and assignees)
                $currentMembers = $project->users()
                    ->whereIn('role', ['creator', 'assignee'])
                    ->select('users.id', 'users.name', 'users.email', 'users.role')
                    ->get();

                // Get available users that can be assigned (creators and assignees)
                $assignedUserIds = $currentMembers->pluck('id')->toArray();
                $availableUsers = User::whereIn('role', ['creator', 'assignee'])
                    ->whereNotIn('id', $assignedUserIds)
                    ->select('id', 'name', 'email', 'role')
                    ->orderBy('name')
                    ->get();

                return [
                    'currentMembers' => $currentMembers,
                    'availableUsers' => $availableUsers
                ];
            });

            return response()->json([
                'success' => true,
                'currentMembers' => $teamData['currentMembers'],
                'availableUsers' => $teamData['availableUsers']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load team data: ' . $e->getMessage(),
                'error' => 'An error occurred while loading team data.'
            ], 500);
        }
    }

    /**
     * Assign members to a project - Updated to support creators and assignees
     */
    public function assignMembers(Request $request, Project $project)
    {
        try {
            $this->authorize('manageMembers', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage this project\'s team.',
                'error' => 'Unauthorized access attempt'
            ], 403);
        }

        $validated = $request->validate([
            'assignee_ids' => 'required|array|min:1',
            'assignee_ids.*' => 'exists:users,id'
        ]);

        try {
            // Verify all users are either creators or assignees
            $validUsers = User::whereIn('id', $validated['assignee_ids'])
                ->whereIn('role', ['creator', 'assignee'])
                ->pluck('id')
                ->toArray();

            if (count($validUsers) !== count($validated['assignee_ids'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected users cannot be assigned to projects. Only creators and assignees can be project members.'
                ], 400);
            }

            // Optimized bulk assignment using sync with existing IDs
            $existingIds = $project->users()->pluck('user_id')->toArray();
            $newIds = array_diff($validUsers, $existingIds);

            if (!empty($newIds)) {
                $project->users()->attach($newIds);
                $newAssignments = count($newIds);
            } else {
                $newAssignments = 0;
            }

            // Clear team data cache
            Cache::forget("project_team_data_{$project->id}");
            $this->clearProjectCaches($request->user());

            $message = $newAssignments > 0
                ? "Successfully assigned {$newAssignments} member(s) to the project."
                : "All selected members were already assigned to this project.";

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign members to project.',
                'error' => 'An error occurred while assigning members.'
            ], 500);
        }
    }

    /**
     * Remove a member from a project - Optimized
     */
    public function removeMember(Request $request, Project $project)
    {
        try {
            $this->authorize('manageMembers', $project);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage this project\'s team.',
                'error' => 'Unauthorized access attempt'
            ], 403);
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:users,id'
        ]);

        try {
            // Optimized check using exists() instead of loading the full model
            if (!$project->users()->where('user_id', $validated['member_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user is not assigned to the project.'
                ], 400);
            }

            // Get member name for response
            $member = User::select('name')->find($validated['member_id']);

            // Remove the assignment
            $project->users()->detach($validated['member_id']);

            // Clear team data cache
            Cache::forget("project_team_data_{$project->id}");
            $this->clearProjectCaches($request->user());

            return response()->json([
                'success' => true,
                'message' => "Successfully removed {$member->name} from the project."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove member from project.',
                'error' => 'An error occurred while removing the member.'
            ], 500);
        }
    }

    /**
     * Organize projects based on user relationship to each project - Optimized
     */
    private function organizeProjectsByUserRelationshipOptimized(User $user, $projects): array
    {
        $organized = [
            'ownProjects' => collect(),
            'assignedProjects' => collect(),
            'otherProjects' => collect(),
        ];

        // Get all project IDs where user is assigned in a single query
        $assignedProjectIds = DB::table('project_user')
            ->where('user_id', $user->id)
            ->pluck('project_id')
            ->toArray();

        foreach ($projects as $project) {
            if ($user->id === $project->created_by) {
                // Projects owned by the user
                $organized['ownProjects']->push($project);
            } elseif (in_array($project->id, $assignedProjectIds)) {
                // Projects where user is assigned (using pre-fetched data)
                $organized['assignedProjects']->push($project);
            } else {
                // All other projects - everyone can see them but only read
                $organized['otherProjects']->push($project);
            }
        }

        return $organized;
    }

    /**
     * Clear project-related caches
     */
    private function clearProjectCaches(User $user): void
    {
        // Clear all project index caches for this user
        $searchTerms = ['', 'test', 'project', 'mobile', 'web', 'app']; // Common search terms
        $sortOptions = ['newest', 'oldest', 'name_asc', 'name_desc'];

        foreach ($searchTerms as $search) {
            foreach ($sortOptions as $sort) {
                Cache::forget("projects_index_{$user->id}_{$search}_{$sort}");
            }
        }

        // Clear team data caches (we don't know which projects might be affected)
        // This is a bit aggressive but ensures consistency
        $projectIds = Project::pluck('id');
        foreach ($projectIds as $projectId) {
            Cache::forget("project_team_data_{$projectId}");
        }
    }
}
