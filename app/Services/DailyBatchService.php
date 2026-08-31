<?php

namespace App\Services;

use App\Enums\ReadingPlanStatus;
use App\Models\Notification;
use App\Models\ReadingPlan;
use Carbon\Carbon;

class DailyBatchService
{
    /**
     * バッチ処理全体（自動失効＋リマインダー）を実行する
     */
    public function executeDailyBatch(): void
    {
        $this->processOverduePlans();
        $this->createReminderNotifications();
    }

    /**
     * 自動失効：　昨日以前の進行中の計画を期限切れにする
     */
    protected function processOverduePlans(): void
    {
        $today = Carbon::today();

        ReadingPlan::where('Status', ReadingPlanStatus::IN_PROGRESS)
            ->whereDate('target_date', '<', $today)
            ->update(['status' => ReadingPlanStatus::OVERDUE]);
    }

    /**
     * リマインダー通知：明日が期日の計画を取得して通知を作成する
     */
    protected function createReminderNotifications(): void
    {
        $tomorrow = Carbon::tomorrow();
        $tomorrowFormatted = $tomorrow->format('Y/m/d');

        ReadingPlan::with('book')
            ->where('status', ReadingPlanStatus::IN_PROGRESS)
            ->whereDate('target_date', '=', $tomorrow)
            ->get()
            ->each(function ($plan) use ($tomorrowFormatted) {
                $bookTitle = $plan->book->title ?? '対象書籍';

                Notification::create([
                    'user_id' => $plan->user_id,
                    'message' => "『{$bookTitle}』の読書計画の期日が明日({$tomorrowFormatted})に迫っています",
                ]);
            });
    }
}
