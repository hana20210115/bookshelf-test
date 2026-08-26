<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Services\ReadingPlanService;
use App\Enums\ReadingPlanStatus;
use Carbon\Carbon;

class ReadingPlanServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReadingPlanService $readingPlanService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->readingPlanService = new ReadingPlanService();
    }

    /**
     * 読書計画の新規作成と削除が正しくされるか検証
     * @void
     */
    public function test_読書計画の新規作成と削除が正しく行われるか():void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'book_id' => $book->id,
            'target_date' => Carbon::tomorrow()->format('Y-m-d'),
        ];

        $readingPlan = $this->readingPlanService->createReadingPlan($user->id,$data);

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => $data['target_date'],
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

        $this->readingPlanService->deleteReadingPlan($readingPlan);
        
        $this->assertDatabaseMissing('reading_plans',
        ['id' =>$readingPlan->id
        ]);
    }

    /**
     * 期限切れの読書計画を未来日で更新すると自動的に進行中ステータスに戻るか検証
     * @void
     */

    public function test_期限切れの読書計画書を未来日で更新すると進行中ステータスに戻るか():void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::yesterday()->format('Y-m-d'),
            'status' => ReadingPlanStatus::OVERDUE,
        ]);

        $updateData = [
            'target_date' => Carbon::tomorrow()->format('Y-m-d'),
        ];

        $this->readingPlanService->updateReadingPlan($readingPlan, $updateData);

        $this->assertDatabaseHas('reading_plans',[
            'id' => $readingPlan->id,
            'target_date' => $updateData['target_date'],
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

    }

    /**
     * 読了アクションを実行するとステータスが完了になり、完了日時が記録されるか検証
     * @void
     */
    public function test_読了アクションを実行するとステータスが完了になり完了日時が記録されるか():void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::tomorrow()->format('Y-m-d'),
            'status' => ReadingPlanStatus::IN_PROGRESS,
        ]);

        $this->readingPlanService->completeReadingPlan($readingPlan);

        $this->assertDatabaseHas('reading_plans',[
            'id' => $readingPlan->id,
            'status' => ReadingPlanStatus::COMPLETED,
        ]);

        $readingPlan->refresh();
        $this->assertNotNull($readingPlan->completed_at);
    }

    
}
