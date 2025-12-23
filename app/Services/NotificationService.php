<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\UserNotification;

class NotificationService
{
    public static function sendNotification(User $user, string $message): void
    {
        $user->notify(new UserNotification($message));
    }
}
