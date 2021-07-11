<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Notifications\ProjectAppllication;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $limit = 20;

    public function all(Request $request)
    {
        $notifications = $request->user()->notifications();
        return $notifications->count() ?
            ResponseHelper::sendSuccess($notifications->simplePaginate($this->limit)) : ResponseHelper::notFound();
    }

    public function unread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications();
        return $notifications->count() ?
            ResponseHelper::sendSuccess($notifications->paginate($this->limit)) : ResponseHelper::notFound();
    }

    public function delete(Request $request)
    {
        $request->validate(["notification_ids" => "array|min:1"]);

        $notifications = $request->user()->notifications()
            ->whereIn('id', $request->notification_ids);

        if (!$notifications->count()) {
            ResponseHelper::notFound();
        }

        return $notifications->delete() ?
            ResponseHelper::sendSuccess([]) : ResponseHelper::notFound();
    }

    public function markAsRead(Request $request)
    {
        $request->validate(["notification_ids" => "array|min:1"]);

        $notifications = $request->user()->notifications()
            ->whereIn('id', $request->notification_ids)
            ->where('notifiable_id', $request->user()->id);

        if (!$notifications->count()) {
            ResponseHelper::notFound();
        }

        return $notifications->update(['read_at' => now()]) ?
            ResponseHelper::sendSuccess([]) : ResponseHelper::notFound();
    }
}
