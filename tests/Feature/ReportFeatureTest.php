<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;
use Carbon\Carbon;

class ReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーがレポート画面にアクセスした際ステータス200が返り集計データが表示されるか検証
     * @return void
     */
    public function ログインユーザーがレポート画面にアクセスした際ステータス200が返り集計データが表示されるか(): void
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
        
        $response->assertViewHas('summary', function ($status) {
            return $status['summary']['books_read'] === 1;
        });
    }
}