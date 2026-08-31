<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 編集画面にアクセスした場合、ステータス200が返るか検証
     *
     * @return void
     */
    public function test_編集画面の表示が正常に行われること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)->get(route('reviews.edit', $review))->assertStatus(200);
    }

    /**
     * 更新および削除の各リクエストを送信した場合、DBが正常に処理され、適切な画面へリダイレクトされるか検証
     *
     * @return void
     */
    public function test_更新と削除処理が正常に行われること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 3,
            'comment' => '元のコメント',
        ]);

        $previousUrl = route('books.show', $book);


        //更新

        $this->actingAs($user)
            ->from($previousUrl)
            ->put(route('reviews.update', $review), [
                'rating' => 5,
                'comment' => '更新されたコメント',
            ])
            ->assertRedirect($previousUrl);


        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'comment' => '更新されたコメント',
        ]);


        // 削除

        $this->actingAs($user)
            ->from($previousUrl)
            ->delete(route('reviews.destroy', $review))
            ->assertRedirect($previousUrl);


        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}