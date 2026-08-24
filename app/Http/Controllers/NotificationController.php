<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * コンストラクタ
     * 
     * @param NOtificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 通知一覧を表示する
     * 
     * @return View
     */
    public function index(): View
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id());

        return view('notifications.index',compact('notifications'));
    }

    /**
     * 通知を既読にする
     * 
     * @param Notification $notification
     * @return RedirectResponse
     */
    public function markAsRead(Notification $notification):RedirectResponse
    {
        abort_if($notification->user_id !== Auth::id(),403);

        $this->notificationService->markAsRead($notification);

        return redirect()->route('notifications.index');
    }
}
