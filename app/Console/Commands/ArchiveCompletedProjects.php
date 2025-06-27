<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

class ArchiveCompletedProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:archive-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive projects where all tasks are completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to archive completed projects...');

        // Get all active projects (not archived)
        $activeProjects = Project::with(['tasks'])
            ->where('status', '!=', 'archived')
            ->get();

        if ($activeProjects->isEmpty()) {
            $this->info('No active projects found. Nothing to archive.');
            return Command::SUCCESS;
        }

        $archivedCount = 0;
        $checkedCount = 0;

        foreach ($activeProjects as $project) {
            $checkedCount++;

            if ($this->shouldArchiveProject($project)) {
                try {
                    $this->archiveProject($project);
                    $archivedCount++;
                    $this->line("✓ Archived project: {$project->name} (ID: {$project->id})");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to archive project: {$project->name}");
                    Log::error('Project archiving failed', [
                        'project_id' => $project->id,
                        'project_name' => $project->name,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                if ($project->tasks->isEmpty()) {
                    $this->line("- Skipped project: {$project->name} (no tasks to complete)");
                } else {
                    $this->line("- Skipped project: {$project->name} (has incomplete tasks)");
                }
            }
        }

        $this->newLine();
        $this->info("Project archiving summary:");
        $this->info("- Total active projects checked: {$checkedCount}");
        $this->info("- Projects archived: {$archivedCount}");
        $this->info("- Projects still active: " . ($checkedCount - $archivedCount));

        return Command::SUCCESS;
    }

    /**
     * Check if a project should be archived
     */
    private function shouldArchiveProject(Project $project): bool
    {
        // If project has no tasks, don't archive it (but show different message)
        if ($project->tasks->isEmpty()) {
            $this->line("  Project: {$project->name} - No tasks found (skipping)");
            return false;
        }

        // Check if all tasks are completed
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();
        $pendingTasks = $project->tasks->where('status', 'pending')->count();
        $inProgressTasks = $project->tasks->where('status', 'in_progress')->count();

        $this->line("  Project: {$project->name} - {$completedTasks}/{$totalTasks} tasks completed");
        $this->line("    Status breakdown: {$completedTasks} completed, {$inProgressTasks} in progress, {$pendingTasks} pending");

        return $totalTasks === $completedTasks;
    }

    /**
     * Archive a project
     */
    private function archiveProject(Project $project): void
    {
        $oldStatus = $project->status;
        $project->status = 'archived';
        $project->save();

        // Log the archiving action
        Log::info('Project archived automatically', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'old_status' => $oldStatus,
            'new_status' => 'archived',
            'total_tasks' => $project->tasks->count(),
            'completed_tasks' => $project->tasks->where('status', 'completed')->count(),
            'archived_at' => now()
        ]);
    }
}
