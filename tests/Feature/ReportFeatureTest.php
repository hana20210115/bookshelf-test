<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーがレポート画面にアクセスした際ステータス200が返り集計データが表示されるか検証
     */
    public function test_ログインユーザーがレポート画面にアクセスした際ステータス200が返り集計データが表示されるか(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::COMPLETED,
            'target_date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->get('/reports');

        $response->assertStatus(200);

        $response->assertViewHas('stats', function ($stats) {
            return $stats['summary']['books_read'] === 1;
        });
    }
}
