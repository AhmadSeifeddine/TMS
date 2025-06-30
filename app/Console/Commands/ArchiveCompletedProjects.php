<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

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

                    $this->archiveProject($project);
                    $archivedCount++;
                    $this->line("✓ Archived project: {$project->name} (ID: {$project->id})");

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
        if ($project->tasks->isEmpty()) {
            $this->line("  Project: {$project->name} - No tasks found (skipping)");
            return false;
        }

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();

        $this->line("  Project: {$project->name} - {$completedTasks}/{$totalTasks} tasks completed");

        return $totalTasks === $completedTasks;
    }

    /**
     * Archive a project
     */
    private function archiveProject(Project $project): void
    {
        $project->status = 'archived';
        $project->save();
    }
}
