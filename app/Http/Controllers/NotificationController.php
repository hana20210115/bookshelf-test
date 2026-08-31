<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * コンストラクタ
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 通知一覧を表示する
     */
    public function index(): View
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id());

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 通知を既読にする
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== Auth::id(), 403);

        $this->notificationService->markAsRead($notification);

        return redirect()->route('notifications.index');
    }
}
