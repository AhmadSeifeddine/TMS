<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Task;
use App\Models\User;
use App\Mail\TaskAssignmentNotification as TaskAssignmentMail;
use Carbon\Carbon;

class SendTaskAssignmentNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The task instance.
     */
    public Task $task;

    /**
     * The assigned user instance.
     */
    public User $assignedUser;

    /**
     * The user who assigned the task.
     */
    public User $assignedBy;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job should run.
     */
    public int $timeout = 60;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [30, 60, 120]; // Wait 30s, then 60s, then 120s between retries
    }

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, User $assignedUser, User $assignedBy)
    {
        $this->task = $task;
        $this->assignedUser = $assignedUser;
        $this->assignedBy = $assignedBy;

        // Set queue and delay if needed
        $this->onQueue('notifications');
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [
            // Prevent overlapping jobs for the same task assignment
            new WithoutOverlapping("task-assignment-{$this->task->id}-{$this->assignedUser->id}")
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if task and users still exist
            if (!$this->task->exists || !$this->assignedUser->exists || !$this->assignedBy->exists) {
                Log::warning('Task assignment notification skipped - entities no longer exist', [
                    'task_id' => $this->task->id,
                    'assigned_user_id' => $this->assignedUser->id,
                    'assigned_by_id' => $this->assignedBy->id
                ]);
                return;
            }

            // Check if task is still assigned to this user
            if ($this->task->assigned_to !== $this->assignedUser->id) {
                Log::info('Task assignment notification skipped - task no longer assigned to this user', [
                    'task_id' => $this->task->id,
                    'original_assigned_user_id' => $this->assignedUser->id,
                    'current_assigned_user_id' => $this->task->assigned_to
                ]);
                return;
            }

            $this->sendNotificationEmail();

            Log::info('Task assignment notification sent successfully', [
                'task_id' => $this->task->id,
                'task_title' => $this->task->title,
                'assigned_user' => $this->assignedUser->email,
                'assigned_by' => $this->assignedBy->email,
                'attempt' => $this->attempts()
            ]);

        } catch (\Exception $e) {
            Log::error('Task assignment notification failed', [
                'task_id' => $this->task->id,
                'assigned_user' => $this->assignedUser->email,
                'assigned_by' => $this->assignedBy->email,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to trigger retry logic
            throw $e;
        }
    }

        /**
     * Send the notification email using Mail class
     */
    private function sendNotificationEmail(): void
    {
        // Create and send the mail instance
        $mail = new TaskAssignmentMail($this->task, $this->assignedUser, $this->assignedBy);

        // Send the email - this will be captured by Telescope
        Mail::send($mail);

        // Log the email details for debugging
        Log::info('Task assignment notification email sent', [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_name' => $this->task->project->name,
            'assigned_user' => $this->assignedUser->email,
            'assigned_user_name' => $this->assignedUser->name,
            'assigned_by' => $this->assignedBy->email,
            'assigned_by_name' => $this->assignedBy->name,
            'priority' => $this->task->priority,
            'due_date' => $this->task->due_date?->format('Y-m-d'),
            'subject' => "New Task Assigned: {$this->task->title}",
            'sent_at' => now()
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Task assignment notification job failed permanently', [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'assigned_user' => $this->assignedUser->email,
            'assigned_by' => $this->assignedBy->email,
            'attempts' => $this->attempts(),
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'failed_at' => now()
        ]);

        // You could also send a notification to administrators about the failure
        // or implement other failure handling logic here
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): Carbon
    {
        return now()->addMinutes(10); // Stop retrying after 10 minutes
    }
}
