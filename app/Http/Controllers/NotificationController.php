<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function all(Request $request)
    {
        $notifications = $request->user()->notifications();

        return $this->sendSuccess(
            $this->paginateMe($notifications)
        );
    }

    public function unread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications();

        return $this->sendSuccess(
            $this->paginateMe($notifications)
        );
    }

    public function delete(Request $request)
    {
        $request->validate(["notification_ids" => "array|min:1"]);

        $delete = $request->user()
            ->notifications()
            ->whereIn('id', $request->notification_ids)
            ->delete();

        return $this->sendSuccess();
    }

    public function markAsRead(Request $request)
    {
        $request->validate(["notification_ids" => "array|min:1"]);

        $update = $request->user()
            ->notifications()
            ->whereIn('id', $request->notification_ids)
            ->where('notifiable_id', $request->user()->id)
            ->update(['read_at' => now()]);

        return $this->sendSuccess();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return ResponseHelper::sendSuccess([]);
    }
}
