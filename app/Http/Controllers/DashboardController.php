<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with dynamic statistics
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Get statistics based on user role
        $stats = $this->getDashboardStats($user);

        // Get recent activities
        $recentActivities = $this->getRecentActivities($user);

        return view('dashboard.index', compact('stats', 'recentActivities'));
    }

    /**
     * Get dashboard statistics based on user role
     */
    private function getDashboardStats($user): array
    {
        $stats = [
            'projects' => [
                'total' => 0,
                'active' => 0,
                'completed' => 0,
                'archived' => 0,
            ],
            'tasks' => [
                'total' => 0,
                'assigned' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'overdue' => 0,
            ],
            'comments' => [
                'total' => 0,
                'this_week' => 0,
            ],
            'productivity' => [
                'completion_rate' => 0,
                'tasks_completed_this_week' => 0,
                'average_task_duration' => 0,
            ]
        ];

        $oneWeekAgo = Carbon::now()->subWeek();
        $today = Carbon::today();

        if ($user->role === 'admin') {
            // Admin sees all data - use optimized queries

            // Get all project stats in one query
            $projectStats = Project::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "archived" THEN 1 ELSE 0 END) as archived
            ')->first();

            $stats['projects'] = [
                'total' => $projectStats->total,
                'active' => $projectStats->active,
                'completed' => $projectStats->completed,
                'archived' => $projectStats->archived,
            ];

            // Get all task stats in one query
            $taskStats = Task::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN due_date < ? AND status IN ("pending", "in_progress") THEN 1 ELSE 0 END) as overdue,
                SUM(CASE WHEN status = "completed" AND updated_at >= ? THEN 1 ELSE 0 END) as completed_this_week
            ', [$today, $oneWeekAgo])->first();

            $stats['tasks'] = [
                'total' => $taskStats->total,
                'assigned' => 0, // Not applicable for admin
                'pending' => $taskStats->pending,
                'in_progress' => $taskStats->in_progress,
                'completed' => $taskStats->completed,
                'overdue' => $taskStats->overdue,
            ];

            $stats['productivity']['tasks_completed_this_week'] = $taskStats->completed_this_week;

            // Get comment stats in one query
            $commentStats = TaskComment::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week
            ', [$oneWeekAgo])->first();

            $stats['comments'] = [
                'total' => $commentStats->total,
                'this_week' => $commentStats->this_week,
            ];

        } else {
            // Non-admin users see only their relevant data
            // Cache the user projects to avoid duplicate calls
            $userProjects = $this->getUserProjects($user);
            $userProjectIds = $userProjects->pluck('id')->toArray();

            // Project stats from cached collection
            $stats['projects']['total'] = $userProjects->count();
            $stats['projects']['active'] = $userProjects->where('status', 'active')->count();
            $stats['projects']['completed'] = $userProjects->where('status', 'completed')->count();
            $stats['projects']['archived'] = $userProjects->where('status', 'archived')->count();

            if (!empty($userProjectIds)) {
                // Get all task stats in one query for user's projects
                $taskStats = Task::whereIn('project_id', $userProjectIds)
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN due_date < ? AND status IN ("pending", "in_progress") THEN 1 ELSE 0 END) as overdue,
                        SUM(CASE WHEN status = "completed" AND updated_at >= ? THEN 1 ELSE 0 END) as completed_this_week
                    ', [$today, $oneWeekAgo])->first();

                $stats['tasks'] = [
                    'total' => $taskStats->total,
                    'assigned' => Task::where('assigned_to', $user->id)->count(),
                    'pending' => $taskStats->pending,
                    'in_progress' => $taskStats->in_progress,
                    'completed' => $taskStats->completed,
                    'overdue' => $taskStats->overdue,
                ];

                $stats['productivity']['tasks_completed_this_week'] = $taskStats->completed_this_week;

                // Get comment stats for user's projects in one query
                $commentStats = TaskComment::whereHas('task', function($query) use ($userProjectIds) {
                    $query->whereIn('project_id', $userProjectIds);
                })->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week
                ', [$oneWeekAgo])->first();

                $stats['comments'] = [
                    'total' => $commentStats->total ?? 0,
                    'this_week' => $commentStats->this_week ?? 0,
                ];
            }
        }

        // Calculate completion rate
        if ($stats['tasks']['total'] > 0) {
            $stats['productivity']['completion_rate'] = round(
                ($stats['tasks']['completed'] / $stats['tasks']['total']) * 100, 1
            );
        }

        return $stats;
    }

    /**
     * Get user's projects based on their role
     */
    private function getUserProjects($user)
    {
        if ($user->role === 'creator') {
            // Creators see their own projects + projects they're assigned to
            return Project::where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                        ->orWhereHas('users', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
            })->get();
        } else {
            // Assignees and members see only projects they're assigned to
            return Project::whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        }
    }

    /**
     * Get recent activities for the user
     */
    private function getRecentActivities($user): array
    {
        $activities = [];
        $sevenDaysAgo = Carbon::now()->subDays(7);

        if ($user->role !== 'admin') {
            // Get recent task assignments with project data in one query
            $recentTasks = Task::where('assigned_to', $user->id)
                ->where('created_at', '>=', $sevenDaysAgo)
                ->with('project:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentTasks as $task) {
                $activities[] = [
                    'type' => 'task_assigned',
                    'message' => "You were assigned to task: {$task->title}",
                    'project' => $task->project->name,
                    'time' => $task->created_at,
                    'priority' => $task->priority,
                ];
            }
        }

        // Combine multiple task queries into one with different conditions
        $userTaskActivities = Task::where('assigned_to', $user->id)
            ->where(function($query) use ($sevenDaysAgo) {
                $query->where(function($subQuery) use ($sevenDaysAgo) {
                    // Completed tasks
                    $subQuery->where('status', 'completed')
                            ->where('updated_at', '>=', $sevenDaysAgo);
                })->orWhere(function($subQuery) use ($sevenDaysAgo) {
                    // Updated in-progress tasks
                    $subQuery->where('status', 'in_progress')
                            ->where('updated_at', '>=', $sevenDaysAgo)
                            ->whereColumn('updated_at', '>', 'created_at');
                });
            })
            ->with('project:id,name')
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get();

        foreach ($userTaskActivities as $task) {
            if ($task->status === 'completed') {
                $activities[] = [
                    'type' => 'task_completed',
                    'message' => "You completed task: {$task->title}",
                    'project' => $task->project->name,
                    'time' => $task->updated_at,
                    'priority' => $task->priority,
                ];
            } elseif ($task->status === 'in_progress') {
                $activities[] = [
                    'type' => 'task_updated',
                    'message' => "You started working on: {$task->title}",
                    'project' => $task->project->name,
                    'time' => $task->updated_at,
                    'priority' => $task->priority,
                ];
            }
        }

        // Get recent comments with task and project data in one query
        $recentComments = TaskComment::where('created_by', $user->id)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->with(['task:id,title,project_id', 'task.project:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentComments as $comment) {
            $activities[] = [
                'type' => 'comment_added',
                'message' => "You commented on: {$comment->task->title}",
                'project' => $comment->task->project->name,
                'time' => $comment->created_at,
                'comment' => substr($comment->comment, 0, 50) . (strlen($comment->comment) > 50 ? '...' : ''),
            ];
        }

        // For creators/admins, get recent projects and tasks created
        if (in_array($user->role, ['creator', 'admin'])) {
            // Get recent projects and tasks created in one combined query using UNION
            $recentCreations = collect();

            // Recent projects
            $recentProjects = Project::where('created_by', $user->id)
                ->where('created_at', '>=', $sevenDaysAgo)
                ->select('id', 'name', 'created_at', DB::raw('"project" as type'))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentProjects as $project) {
                $activities[] = [
                    'type' => 'project_created',
                    'message' => "You created project: {$project->name}",
                    'project' => $project->name,
                    'time' => $project->created_at,
                ];
            }

            // Recent tasks created
            $recentTasksCreated = Task::where('created_by', $user->id)
                ->where('created_at', '>=', $sevenDaysAgo)
                ->with('project:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentTasksCreated as $task) {
                $activities[] = [
                    'type' => 'task_created',
                    'message' => "You created task: {$task->title}",
                    'project' => $task->project->name,
                    'time' => $task->created_at,
                    'priority' => $task->priority,
                ];
            }
        }

        // Sort activities by time (most recent first)
        usort($activities, function ($a, $b) {
            return $b['time']->timestamp - $a['time']->timestamp;
        });

        return array_slice($activities, 0, 10); // Return top 10 activities
    }
}
