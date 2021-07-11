<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetRequestNotification extends Notification
{
    use Queueable;

    public $accessCode;
    public $url;

    public function __construct(string $accessCode)
    {
        $this->accessCode = $accessCode;
        $this->url = "tasks.test/{$accessCode}";
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting("Hi {$notifiable->name}")
            ->line("A password reset request was made on your account.")
            ->line("Kindly use the code below to complete your password reset {$this->accessCode}")
            ->action('Complete request', url($this->url))
            ->line("If you did not initiate this, Kindly disregard this message")
            ->line('Thank you for using our application!');
    }


    public function toArray($notifiable)
    {
        return [
            "intro" => "Hi {$notifiable->name},",
            "from" => "3HJOBS Support",
            "subject" => "Password Reset",
            "body" => "Kindly use the code below to complete your password reset {$this->accessCode}",
            "link" =>  url($this->url),
            "outro1" => "Best Regards,",
            "outro2" => "3HJOBS",
        ];
    }
}
