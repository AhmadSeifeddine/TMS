<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDueTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-due-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for tasks due today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to send due task reminders...');

        // Get all tasks due today that are not completed
        $today = Carbon::today();
        $dueTasks = Task::with(['assignedUser', 'project', 'creator'])
            ->whereDate('due_date', $today)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('assigned_to')
            ->get();

        if ($dueTasks->isEmpty()) {
            $this->info('No tasks due today. No reminders sent.');
            return Command::SUCCESS;
        }

        $remindersSent = 0;
        $errors = 0;

        foreach ($dueTasks as $task) {
            try {
                $this->sendTaskReminder($task);
                $remindersSent++;
                $this->line("✓ Reminder sent for task: {$task->title} to {$task->assignedUser->email}");
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Failed to send reminder for task: {$task->title}");
                Log::error('Task reminder failed', [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'assigned_user' => $task->assignedUser?->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info("Task reminder summary:");
        $this->info("- Total tasks due today: {$dueTasks->count()}");
        $this->info("- Reminders sent successfully: {$remindersSent}");

        if ($errors > 0) {
            $this->warn("- Failed reminders: {$errors}");
        }

        return Command::SUCCESS;
    }

    /**
     * Send reminder email for a specific task
     */
    private function sendTaskReminder(Task $task)
    {
        $user = $task->assignedUser;
        $project = $task->project;

        // Simulate sending email by logging the email content
        $emailContent = [
            'to' => $user->email,
            'subject' => "Task Due Today: {$task->title}",
            'message' => "
Dear {$user->name},

This is a reminder that the following task is due today:

Task: {$task->title}
Project: {$project->name}
Priority: " . ucfirst($task->priority) . "
Status: " . ucfirst(str_replace('_', ' ', $task->status)) . "
Due Date: " . $task->due_date->format('F j, Y') . "

" . ($task->description ? "Description: {$task->description}" : "") . "

Please make sure to complete this task on time.

Best regards,
Task Management System
            "
        ];

        // Log the email instead of actually sending it
        Log::info('Task due reminder email sent', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'project_name' => $project->name,
            'assigned_user' => $user->email,
            'user_name' => $user->name,
            'due_date' => $task->due_date->format('Y-m-d'),
            'priority' => $task->priority,
            'status' => $task->status,
            'email_content' => $emailContent
        ]);

    }
}
