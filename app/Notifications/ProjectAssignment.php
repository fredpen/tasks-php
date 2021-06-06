<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class ProjectAssignment extends Notification
{
    private $project_id;

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
            ->line('This is to notify you that a new Task been assigned to you on our platform, Click the button below or log in to your profile to accept the task')
            ->line('After accepting the task, we will contact you for details')
           
            ->line('Thanks for using impromptuTasks!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => "Hi, A Task has been assigned to you",
            'subject' => 'This is to notify you that a new Task has been assigned to you, Visit your profile to accept the task and start earning '
        ];
    }
}