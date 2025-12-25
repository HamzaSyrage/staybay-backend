<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function myNotifications(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'unread' => NotificationResource::collection($user->unreadNotifications),
            'read'   => NotificationResource::collection($user->readNotifications),
        ]);
    }
    public function markAsRead($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();
//        dd("fuck".$notification);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'code' => 200
        ]);
    }
    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return response()->noContent();
    }
}
