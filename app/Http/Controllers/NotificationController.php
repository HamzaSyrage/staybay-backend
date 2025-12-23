<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function myNotifications(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'unread' => $user->unreadNotifications,
            'read'   => $user->readNotifications,
        ]);
    }
}
