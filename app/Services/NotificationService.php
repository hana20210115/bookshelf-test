<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * 指定したユーザーの通知一覧を新しい順に取得する
     * 
     * @param int $userId
     * @return Collection
     */
    public function getUserNotifications(int $userId):Collection
    {
        return Notification::where('user_id',$userId)
        ->latest()
        ->get();
    }

    /**
     * 通知を既読(現在時刻で更新)する
     * 
     * @param Notification $notification
     * @return bool
     */
    public function markAsRead(Notification $notification):bool
    {
        return $notification->update(['read_at' => now()]);
    }
}

