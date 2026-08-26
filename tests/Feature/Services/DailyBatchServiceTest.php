<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Services\DailyBatchService;
use App\Enums\ReadingPlanStatus;
use Carbon\Carbon;

class DailyBatchServiceTest extends TestCase
{
    use RefreshDatabase;

    private DailyBatchService $dailyBatchService;

    protected function setUp():void
    {
        parent::setUP();
        $this->dailyBatchService = new
        DailyBatchService();
    }

    /**
     * 進行中で期日が昨日以前のデータのみが期限切れに更新されるか検証
     * @void
     */
    public function test_進行中で期日が昨日以前のデータのみが期限切れに更新されること():void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        //失効対象データ
        $targetPlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::yesterDay()->format('Y-m-d'),
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

        //失効対象外データ
        $excludePlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::tomorrow()->format('Y-m-d'),
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

        $this->dailyBatchService->executeDailyBatch();

        $this->assertDatabaseHas('reading_plans',[
            'id' => $targetPlan->id,
            'status' => ReadingPlanStatus::OVERDUE,
        ]);

        $this->assertDatabaseHas('reading_plans',[
            'id' => $excludePlan->id,
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);


    }

    /**
     * 進行中で期日が明日のデータのみリマインダー通知が作成されるか検証
     * @void
     */
    public function test_進行中で明日が期日のデータのみリマインダー通知が作成されること():void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        //通知対象データ
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::tomorrow()->format('Y/m/d'),
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

        $this->dailyBatchService->executeDailyBatch();

        $tomorrowFormatted = Carbon::tomorrow()->format('Y/m/d');

        $this->assertDatabaseHas('notifications',[
            'user_id' => $user->id,
            'message' => "『{$book->title}』の読書計画の期日が明日({$tomorrowFormatted})に迫っています",
        ]);
    }

}
