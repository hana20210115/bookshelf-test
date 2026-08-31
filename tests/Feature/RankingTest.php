<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ランキング画面にアクセスした場合、評価の平均が降順に表示されるか検証
     */
    public function test_ランキング画面にアクセスした場合評価の平均が降順で表示される(): void
    {
        // 3つの書籍データを作成
        $book1 = Book::factory()->create(['title' => 'Book A']);

        $book2 = Book::factory()->create(['title' => 'Book B']);

        $book3 = Book::factory()->create(['title' => 'Book C']);

        // それぞれの書籍データにレビューを投稿する
        Review::factory()->create([
            'book_id' => $book1->id, 'rating' => 5,
        ]);

        Review::factory()->create([
            'book_id' => $book2->id, 'rating' => 3,
        ]);

        Review::factory()->create([
            'book_id' => $book3->id, 'rating' => 1,
        ]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $book1->title,
            $book2->title,
            $book3->title,
        ]);
    }
}
