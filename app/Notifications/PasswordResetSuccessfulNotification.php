<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetSuccessfulNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }


    public function toMail($notifiable)
    {
        $name = $notifiable->name;

        return (new MailMessage)
            ->greeting("Hi {$name}")
            ->line("Your password reset was successful !")
            ->line("If you did not initiate this, Kindly contact our support for security assessment")
            ->line('Thank you for using our application!');
    }


    public function toArray($notifiable)
    {
        return [
            "intro" => "Hi {$notifiable->name},",
            "from" => "3HJOBS Support",
            "subject" => "Password Reset",
            "body" => "Your password reset was successful !",
            "link" =>  null,
            "outro1" => "Best Regards,",
            "outro2" => "3HJOBS",
        ];
    }
}
