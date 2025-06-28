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

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->sendNotificationEmail();
    }

        /**
     * Send the notification email using Mail class
     */
    private function sendNotificationEmail(): void
    {
        $mail = new TaskAssignmentMail($this->task, $this->assignedUser, $this->assignedBy);

        Mail::send($mail);
    }

    public function retryUntil(): Carbon
    {
        return now()->addMinutes(10);
    }
}
