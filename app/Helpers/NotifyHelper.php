<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\GeneralNotification;

class NotifyHelper
{

    public static function talkTo(User $user, $messageId)
    {
        $messageData = Self::setMessage($messageId);

        $from = array_key_exists("from", $messageData) ? $messageData['from'] : "3HJOBS Support";
        $link = array_key_exists("link", $messageData) ? $messageData['link'] : false;
        $subject = array_key_exists("subject", $messageData) ? $messageData['subject'] : "Message from 3HJOBS Support";
        $body = array_key_exists("body", $messageData) ? $messageData['body'] : "Thanks for using our services";
        $sendMail = !!$messageData['sendMail'];

        $user->notify(new GeneralNotification($subject, $body, $link, $from, $sendMail));

    }

    private static function setMessage($messageId)
    {
        switch ($messageId) {
            case 'account_update':
                return  [
                    "subject" => "Account Update",
                    "body" => "This is to notify you that an update has been on your account",
                    "sendMail" => true
                ];
                break;

            case 'account_creation':
                return  [
                    "subject" => "Account creation",
                    "body" => "Welcome to 3HJOBS",
                    "sendMail" => true
                ];
                break;

            case 'login':
                return  [
                    "subject" => "Login Notification",
                    "body" => "Login action",
                    "sendMail" => false
                ];
                break;

            case 'project_application':
                return  [
                    "subject" => "Project Application",
                    "body" => "This is to notify you that your application is successful, We will contact you soonest",
                    "sendMail" => true
                ];
                break;

            case 'project_withdrawal':
                return  [
                    "subject" => "Project Withdrawal",
                    "body" => "This is to notify you that Task been withdrawn from you",
                    "sendMail" => true
                ];
                break;

                case 'project_cancelled':
                return  [
                    "subject" => "Project Cancelled",
                    "body" => "This is to notify you that your project has been cancelled successfully ",
                    "sendMail" => false
                ];
                break;

            case 'project_completed':
                return  [
                    "subject" => "Project Completed",
                    "body" => "This is to notify you that your project has been completed successfully ",
                    "sendMail" => true
                ];
                break;

            case 'project_created':
                return  [
                    "subject" => "Project Created",
                    "body" => "This is to notify you that your new project has been created, Finish the process, A Task Master awaits",
                    "sendMail" => true
                ];
                break;


            case 'project_published':
                return  [
                    "subject" => "Project Published",
                    "body" => "This is to notify you that your new project is live, We will contact you when a Task master takes up your task.",
                    "sendMail" => true
                ];
                break;

            case 'project_assigned':
                return  [
                    "subject" => "Project Assigned",
                    "body" => "This is to notify you that your new project is live, We will contact you when a Task master takes up your task.",
                    "sendMail" => true
                ];
                break;

            case 'project_payment':
                return  [
                    "subject" => "Project Payment",
                    "body" => "This is to notify you that your payment was succesfull.",
                    "sendMail" => true
                ];
                break;

            default:
                return [
                    "title" => "Account Update",
                    "subject" => "This is to notify you that an update has been on your account",
                    "sendMail" => false
                ];
                break;
        }
    }
}
