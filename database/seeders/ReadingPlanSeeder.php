<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\ReadingPlan;
use APP\Models\User;
use App\Enum\ReadingPlanStatus;
use Carbon\Carbon;

/**
 * 読書計画のテストデータを投入するシーダークラス
 */
class ReadingPlanSeeder extends Seeder
{
    /**
     * データベースへデータを投入する
     * @return void
     */
    public function run(): void
    {
        // ID:1のユーザー（主要テストと動作確認用）と、ID:2のユーザー（認可テスト用)を取得
        $mainUser = User::findOrFail(1);
        $otherUser = User::findOrFail(2);

        //一応ユーザーが存在しない場合はスキップするようにしておきます
        if (!$mainUser){
            return;
        }

        //ユーザー１のダミーデータ(主要テスト)

        //進行中：期日が7日後
        ReadingPlan::create([
            'user_id' => $mainUser->id,
            'book_id' => 1,
            'target_date' => Carbon::today()->addDays(7),
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

        //進行中:期日が明日＝通知機能のテスト対象のデータ
        ReadingPlan::create([
            'user_id' => $mainUser->id,
            'book_id' => 2,
            'target_date' => Carbon::today()->addDays(1),
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

        //期限切れ：期日が３日前＝失効処理のテスト対象
        ReadingPlan::create([
            'user_id' => $mainUser->id,
            'book_id' => 3,
            'target_date' => Carbon::today()->subDays(3),
            'status' => ReadingPlanStatus::OVERDUE,
        ]);

        //読了:期日が機能＝完了済みのため何のしない
        ReadingPlan::create([
            'user_id' => $mainUSer->id,
            'book_id' => 4,
            'target_date' => Carbon::today()->subDays(3),
            'status' => ReadingPlanStatus::COMPLETED,
        ]);

        //ユーザー２（認可、他人のデータが見えないかテスト）
        if($otherUser) {
            ReadingPlan::create([
                'user_id' => $otherUser->id,
                'book_id' => 1,
                'target_date' => Carbon::today()->addDays(3),
                'status' => ReadingPlanStatus::IN_PROGRESS,
            ]);
        }

    }
}
