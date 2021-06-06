<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Auth;

use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class projectCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $tries = 3;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hi ' . $notifiable->name . ",")
            ->line('This is to notify you that your new project has been created, Finish the process, Task Master awaits')
            ->action('impromptuTasks', route('home'))
            ->line('Thanks for using impromptuTasks!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => "Hi, Your Project has been created",
            'subject' => 'This is to notify you that your new project has been created, Fill the necessary fields and post it, Task Master awaits'
        ];
    }
}
