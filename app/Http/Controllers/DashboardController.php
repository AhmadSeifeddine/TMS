<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use Carbon\Carbon;

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

        if ($user->role === 'admin') {
            // Admin sees all data
            $stats['projects']['total'] = Project::count();
            $stats['projects']['active'] = Project::where('status', 'active')->count();
            $stats['projects']['completed'] = Project::where('status', 'completed')->count();
            $stats['projects']['archived'] = Project::where('status', 'archived')->count();

            $stats['tasks']['total'] = Task::count();
            $stats['tasks']['pending'] = Task::where('status', 'pending')->count();
            $stats['tasks']['in_progress'] = Task::where('status', 'in_progress')->count();
            $stats['tasks']['completed'] = Task::where('status', 'completed')->count();
            $stats['tasks']['overdue'] = Task::where('due_date', '<', Carbon::today())
                ->whereIn('status', ['pending', 'in_progress'])->count();

            $stats['comments']['total'] = TaskComment::count();
        } else {
            // Non-admin users see only their relevant data
            $userProjects = $this->getUserProjects($user);
            $userProjectIds = $userProjects->pluck('id')->toArray();

            $stats['projects']['total'] = $userProjects->count();
            $stats['projects']['active'] = $userProjects->where('status', 'active')->count();
            $stats['projects']['completed'] = $userProjects->where('status', 'completed')->count();
            $stats['projects']['archived'] = $userProjects->where('status', 'archived')->count();

            // Tasks from user's projects
            $userTasks = Task::whereIn('project_id', $userProjectIds);
            $stats['tasks']['total'] = $userTasks->count();
            $stats['tasks']['pending'] = $userTasks->where('status', 'pending')->count();
            $stats['tasks']['in_progress'] = $userTasks->where('status', 'in_progress')->count();
            $stats['tasks']['completed'] = $userTasks->where('status', 'completed')->count();

            // Tasks specifically assigned to this user
            $stats['tasks']['assigned'] = Task::where('assigned_to', $user->id)->count();

            // Overdue tasks in user's projects
            $stats['tasks']['overdue'] = Task::whereIn('project_id', $userProjectIds)
                ->where('due_date', '<', Carbon::today())
                ->whereIn('status', ['pending', 'in_progress'])->count();

            // Comments in user's projects
            $stats['comments']['total'] = TaskComment::whereHas('task', function($query) use ($userProjectIds) {
                $query->whereIn('project_id', $userProjectIds);
            })->count();
        }

        // Common calculations for all users
        $oneWeekAgo = Carbon::now()->subWeek();

        if ($user->role === 'admin') {
            $stats['comments']['this_week'] = TaskComment::where('created_at', '>=', $oneWeekAgo)->count();
            $stats['productivity']['tasks_completed_this_week'] = Task::where('status', 'completed')
                ->where('updated_at', '>=', $oneWeekAgo)->count();
        } else {
            $userProjectIds = $this->getUserProjects($user)->pluck('id')->toArray();

            $stats['comments']['this_week'] = TaskComment::whereHas('task', function($query) use ($userProjectIds) {
                $query->whereIn('project_id', $userProjectIds);
            })->where('created_at', '>=', $oneWeekAgo)->count();

            $stats['productivity']['tasks_completed_this_week'] = Task::whereIn('project_id', $userProjectIds)
                ->where('status', 'completed')
                ->where('updated_at', '>=', $oneWeekAgo)->count();
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

        // Recent tasks assigned to user (last 7 days)
        if ($user->role !== 'admin') {
            $recentTasks = Task::where('assigned_to', $user->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->with(['project'])
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

        // Recent task completions by user (last 7 days)
        $completedTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->with(['project'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($completedTasks as $task) {
            $activities[] = [
                'type' => 'task_completed',
                'message' => "You completed task: {$task->title}",
                'project' => $task->project->name,
                'time' => $task->updated_at,
                'priority' => $task->priority,
            ];
        }

        // Recent task status updates by user (last 7 days)
        $updatedTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['in_progress'])
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->where('updated_at', '>', 'created_at') // Only tasks that were actually updated
            ->with(['project'])
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($updatedTasks as $task) {
            $activities[] = [
                'type' => 'task_updated',
                'message' => "You started working on: {$task->title}",
                'project' => $task->project->name,
                'time' => $task->updated_at,
                'priority' => $task->priority,
            ];
        }

        // Recent comments by user (last 7 days)
        $recentComments = TaskComment::where('created_by', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['task.project'])
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

        // Recent projects created by user (for creators/admins)
        if (in_array($user->role, ['creator', 'admin'])) {
            $recentProjects = Project::where('created_by', $user->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
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
        }

        // Recent tasks created by user (for creators/admins)
        if (in_array($user->role, ['creator', 'admin'])) {
            $recentTasksCreated = Task::where('created_by', $user->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->with(['project'])
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
