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
        $link = array_key_exists("link", $messageData) ? $messageData['link'] : "";
        $subject = array_key_exists("subject", $messageData) ? $messageData['subject'] : "Message from 3HJOBS Support";
        $body = array_key_exists("body", $messageData) ? $messageData['body'] : "Thanks for using our services";

        // try {
        //     //code...
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }
        $user->notify(new GeneralNotification($subject, $body, $link, $from));
    }

    private static function setMessage($messageId)
    {
        switch ($messageId) {
            case 'account_update':
                return  [
                    "subject" => "Account Update",
                    "body" => "This is to notify you that an update has been on your account"
                ];
                break;

            case 'account_creation':
                return  [
                    "subject" => "Account creation",
                    "body" => "Welcome to 3HJOBS"
                ];
                break;

                case 'login':
                return  [
                    "subject" => "Login Notification",
                    "body" => "Login action"
                ];
                break;

            default:
                return [
                    "title" => "Account Update",
                    "subject" => "This is to notify you that an update has been on your account"
                ];
                break;
        }
    }
}
