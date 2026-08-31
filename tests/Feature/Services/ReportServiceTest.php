<?php

namespace Tests\Feature\Services;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\Genre;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reportService = new ReportService;
    }

    /**
     * 基本サマリーが正しく集計されるか検証
     *
     * @void
     */
    public function test_基本サマリーが正しく集計されること(): void
    {
        $user = User::factory()->create();
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        // レビューの準備、平均評価3.5になる
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'rating' => 2,
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'rating' => 5,
        ]);

        // 読書計画の準備
        // book1:1回目完了
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'status' => ReadingPlanStatus::COMPLETED,
            'target_date' => Carbon::now()->format('Y-m-d'),
        ]);

        // book2:2回目完了同じ本を2回読んだ想定で重複させる
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'status' => ReadingPlanStatus::COMPLETED,
            'target_date' => Carbon::now()->format('Y-m-d'),
        ]);
        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'status' => ReadingPlanStatus::COMPLETED,
            'target_date' => Carbon::now()->format('Y-m-d'),
        ]);

        $result = $this->reportService->getSummary($user->id);

        $this->assertEquals(2, $result['total_reviews']);
        $this->assertEquals(3.5, $result['average_rating']);
        $this->assertEquals(2, $result['completed_books_count']);

    }

    /**
     * 高評価書籍TOP5が評価４以上かつ降順で最大5件取得でき、同点時は新しい順になるか検証
     *
     * @void
     */
    public function test_高評価書籍_to_p5が正しく取得できるか(): void
    {
        $user = User::factory()->create();

        // 評価３のデータ（４未満なので除外されるべき)
        $book1 = Book::factory()->create([
            'title' => 'Book 1',
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'rating' => 3,
        ]);

        // 評価5のデータ（同点時create_at順になっているか確認するデータ）
        $book2 = Book::factory()->create([
            'title' => 'Book 2',
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'rating' => 5,
            'created_at' => now()->subDay(2),
        ]);

        // これが一位になるはず
        $book3 = Book::factory()->create([
            'title' => 'Book 3',
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book3->id,
            'rating' => 5,
            'created_at' => now()->subDay(1),
        ]);

        // 評価4のデータ(件数制限の確認用)
        $book4 = Book::factory()->create(['title' => 'Book 4']);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book4->id, 'rating' => 4, 'created_at' => Carbon::now()->subDays(3)]);

        $book5 = Book::factory()->create(['title' => 'Book 5']);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book5->id, 'rating' => 4, 'created_at' => Carbon::now()->subDays(2)]);

        $book6 = Book::factory()->create(['title' => 'Book 6']);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book6->id, 'rating' => 4, 'created_at' => Carbon::now()->subDays(1)]);

        // これが押し出されるはず
        $book7 = Book::factory()->create(['title' => 'Book 7']);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book7->id, 'rating' => 4, 'created_at' => Carbon::now()->subDays(4)]);

        $result = $this->reportService->getTopRatedBooks($user->id, 5);

        // 最大５件で取得できているか
        $this->assertCount(5, $result);

        $this->assertEquals('Book 3', $result[0]['title']);
        $this->assertEquals('Book 2', $result[1]['title']);
        $this->assertEquals('Book 6', $result[2]['title']);
        $this->assertEquals('Book 5', $result[3]['title']);
        $this->assertEquals('Book 4', $result[4]['title']);

    }

    /**
     * ジャンル別評価傾向の平均点が正確に計算され降順で取得できるか検証():void
     */
    public function test_ジャンル別評価傾向の平均点が正確に計算され降順で取得できるか(): void
    {
        $user = User::factory()->create();

        // ジャンル作成
        $genreA = Genre::factory()->create([
            'name' => 'IT・技術',
        ]);
        $genreB = Genre::factory()->create([
            'name' => 'ビジネス',
        ]);
        $genreC = Genre::factory()->create([
            'name' => '小説',
        ]);

        // IT・技術とビジネスの２つのジャンルを持つ書籍
        $book1 = Book::factory()->create();
        $book1->genres()->attach([
            $genreA->id,
            $genreB->id,
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'rating' => 4,
        ]);

        // IT・技術のみ持つ書籍　IT・技術の平均は4.5になるはず
        $book2 = Book::factory()->create();
        $book2->genres()->attach([
            $genreA->id,
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'rating' => 5,
        ]);

        // 小説のみ持つ
        $book3 = Book::factory()->create();
        $book3->genres()->attach([
            $genreC->id,
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book3->id,
            'rating' => 3,
        ]);

        $result = $this->reportService->getTopGenres($user->id, 5);

        $this->assertCount(3, $result);

        $this->assertEquals('IT・技術', $result[0]['name']);
        $this->assertEquals(4.5, $result[0]['average_rating']);

        $this->assertEquals('ビジネス', $result[1]['name']);
        $this->assertEquals(4.0, $result[1]['average_rating']);

        $this->assertEquals('小説', $result[2]['name']);
        $this->assertEquals(3.0, $result[2]['average_rating']);
    }
}
