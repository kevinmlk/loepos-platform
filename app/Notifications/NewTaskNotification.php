<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Task;

class NewTaskNotification extends Notification
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('A new task has been assigned to you.')
            ->line('Task: ' . $this->task->description)
            ->line('Urgency: ' . $this->task->urgency)
            ->line('Due Date: ' . $this->task->due_date->format('d-m-Y'))
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Loepos')
            ->line('Slimme oplossingen, heldere toekomst');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'description' => $this->task->description,
            'urgency' => $this->task->urgency,
            'due_date' => $this->task->due_date,
        ];
    }
}
