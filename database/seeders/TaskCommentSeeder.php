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
        // Get the 4 specific test accounts
        $admin = User::where('email', 'admin@company.com')->first();
        $creator = User::where('email', 'creator@company.com')->first();
        $assignee = User::where('email', 'assignee@company.com')->first();
        $member = User::where('email', 'member@company.com')->first();

        $tasks = Task::with(['project.users', 'project.creator'])->get();

        // Specific comments for each user type
        $adminComments = [
            "Great progress on this task! The implementation looks solid from an architectural perspective.",
            "I've reviewed the security aspects and everything looks good.",
            "Could we add some additional monitoring for this feature?",
            "Performance metrics look excellent. Well done team!",
            "I've approved the deployment to production.",
            "The system integration is working flawlessly.",
            "Budget and timeline are on track for this task.",
            "Cross-functional coordination has been excellent on this one.",
            "Security audit completed - no issues found."
        ];

        $creatorComments = [
            "Thanks for the progress update! This aligns with our project goals.",
            "I've updated the requirements based on stakeholder feedback.",
            "The design looks fantastic! Ready for client review.",
            "Can we add some unit tests for this functionality?",
            "Documentation has been updated to reflect these changes.",
            "Client feedback incorporated successfully.",
            "The feature meets all acceptance criteria.",
            "Great collaboration between team members on this task.",
            "This implementation exceeds our original expectations."
        ];

        $assigneeComments = [
            "Task completed successfully! All test cases are passing.",
            "I'm blocked on this task. Can someone help with the API integration?",
            "Working on integrating the third-party service as requested.",
            "Found a small bug in the validation logic. Fixed it in the latest commit.",
            "Testing completed across multiple browsers and devices.",
            "Performance optimization implemented - load time improved by 30%.",
            "Code review feedback has been addressed.",
            "Mobile responsiveness verified and working perfectly.",
            "Ready for QA testing and deployment preparation."
        ];

        $memberComments = [
            "Thanks for including me in this project!",
            "The UI looks great from a user perspective.",
            "Happy to help with testing when needed.",
            "Good progress on this task.",
            "Looking forward to seeing this feature in production."
        ];

        $commentsByUser = [
            $admin->id => $adminComments,
            $creator->id => $creatorComments,
            $assignee->id => $assigneeComments,
            $member->id => $memberComments,
        ];

        $adminCommentCount = 0;
        $creatorCommentCount = 0;
        $assigneeCommentCount = 0;
        $memberCommentCount = 0;

        foreach ($tasks as $task) {
            // Get users who can comment on this task (project members + project creator)
            $projectUsers = $task->project->users;
            $projectCreator = $task->project->creator;

            // Combine project users and creator, ensuring no duplicates
            $eligibleCommenters = $projectUsers->push($projectCreator)->unique('id');

            // Skip if no eligible commenters
            if ($eligibleCommenters->isEmpty()) {
                $this->command->warn("Task '{$task->title}' has no eligible commenters. Skipping comments.");
                continue;
            }

            // Add 2-4 comments per task, ensuring our test accounts get priority
            $commentCount = rand(2, 4);

            for ($i = 0; $i < $commentCount; $i++) {
                // Prioritize comments from admin, creator, assignee
                $commenter = null;
                $comment = '';

                if ($i === 0 && $eligibleCommenters->contains($assignee)) {
                    // First comment often from assignee (task performer)
                    $commenter = $assignee;
                    $comment = $assigneeComments[array_rand($assigneeComments)];
                    $assigneeCommentCount++;
                } elseif ($i === 1 && $eligibleCommenters->contains($creator)) {
                    // Second comment from creator (project owner feedback)
                    $commenter = $creator;
                    $comment = $creatorComments[array_rand($creatorComments)];
                    $creatorCommentCount++;
                } elseif ($i === 2 && $eligibleCommenters->contains($admin)) {
                    // Third comment from admin (oversight/approval)
                    $commenter = $admin;
                    $comment = $adminComments[array_rand($adminComments)];
                    $adminCommentCount++;
                } else {
                    // Random commenter from eligible users
                    $commenter = $eligibleCommenters->random();
                    $userComments = $commentsByUser[$commenter->id] ?? $memberComments;
                    $comment = $userComments[array_rand($userComments)];

                    if ($commenter->id === $member->id) {
                        $memberCommentCount++;
                    }
                }

                TaskComment::create([
                    'task_id' => $task->id,
                    'comment' => $comment,
                    'created_by' => $commenter->id,
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
            }
        }

        $this->command->info('Created comments for all tasks with specific user participation:');
        $this->command->info("- Admin made {$adminCommentCount} comments");
        $this->command->info("- Creator made {$creatorCommentCount} comments");
        $this->command->info("- Assignee made {$assigneeCommentCount} comments");
        $this->command->info("- Member made {$memberCommentCount} comments");
    }
}
