<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $accessCode)
    {
        $this->accessCode = $accessCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $name = $notifiable->name;
        $code = $this->accessCode;
        $url = "tasks.test/{$code}";

        return (new MailMessage)
            ->greeting("Hi {$name}")
            ->line("A password reset request was made on your account.")
            ->line("Kindly use the code below to complete your password reset {$code}")
            ->action('Complete request', url($url))
            ->line("If you did not initiate this, Kindly disregard this message")
            ->line('Thank you for using our application!');
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
            //
        ];
    }
}
