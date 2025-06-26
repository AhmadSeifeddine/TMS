<?php

namespace Database\Seeders;

use App\Models\TaskComment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();
        $users = User::all();

        $comments = [
            "Great progress on this task! The implementation looks solid.",
            "I've reviewed the code and left some feedback in the PR.",
            "Could we add some error handling for edge cases?",
            "The design looks fantastic! Ready for client review.",
            "I'm blocked on this task. Can someone help with the API integration?",
            "Testing completed successfully. All cases pass.",
            "Updated the requirements based on client feedback.",
            "Performance improvement implemented. Load time reduced by 40%.",
            "Documentation has been updated to reflect the latest changes.",
            "Security review completed. No vulnerabilities found.",
            "The feature is ready for production deployment.",
            "Found a bug in the validation logic. Working on a fix.",
            "User feedback incorporated. UX improvements made.",
            "Database optimization complete. Queries are much faster now.",
            "Mobile responsiveness tested across different devices.",
            "API rate limiting configured successfully.",
            "Unit tests added with 95% code coverage.",
            "Code review completed. LGTM! 👍",
            "Deployment scheduled for tomorrow evening.",
            "Task completed ahead of schedule! 🎉",
            "Need clarification on the business requirements.",
            "Working on integrating the third-party service.",
            "UI components are reusable and well-documented.",
            "Performance monitoring dashboard is now live.",
            "Error logs show significant improvement after the fix.",
            "Client approved the mockups. Moving to development.",
            "Database migration scripts are ready for production.",
            "Load testing results look promising.",
            "Feature flag implementation allows safe rollout.",
            "Team collaboration on this task was excellent!",
            "Cross-browser compatibility verified.",
            "Accessibility standards compliance checked.",
            "The caching strategy is working as expected.",
            "Monitoring alerts configured for this feature.",
            "Code refactoring improved maintainability.",
        ];

        foreach ($tasks as $task) {
            // Add 1-5 comments per task randomly
            $commentCount = rand(1, 5);

            for ($i = 0; $i < $commentCount; $i++) {
                TaskComment::create([
                    'task_id' => $task->id,
                    'comment' => $comments[array_rand($comments)],
                    'created_by' => $users->random()->id,
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
            }
        }

        $this->command->info('Created comments for all tasks.');
    }
}
