<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use App\Models\User;

class TaskAssignmentNotification extends Mailable
{
    use Queueable, SerializesModels;

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
     * Create a new message instance.
     */
    public function __construct(Task $task, User $assignedUser, User $assignedBy)
    {
        $this->task = $task;
        $this->assignedUser = $assignedUser;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->assignedUser->email,
            subject: "New Task Assigned: {$this->task->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.task-assignment-notification',
            with: [
                'task' => $this->task,
                'assignedUser' => $this->assignedUser,
                'assignedBy' => $this->assignedBy,
                'project' => $this->task->project,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int,
     */
    public function attachments(): array
    {
        return [];
    }
}
