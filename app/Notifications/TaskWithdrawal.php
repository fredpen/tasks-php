<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskWithdrawal extends Notification
{
    protected $project_id;

    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hi ' . $notifiable->name . ",")
            ->line('This is to notify you that Task been withdrawn from you')
            ->line('Thanks for using impromptuTasks!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => "Hi, A Task has been withdrwn from you",
            'subject' => 'This is to notify you that a new Task has been withdrwn from you'
        ];
    }
}
