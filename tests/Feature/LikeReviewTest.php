<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LikeReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーがレビューにいいねすると(DB.like_review_table)に保存され元の画面へリダイレクトされるか検証 302 Found
     */
    public function test_ログインユーザーがレビューにいいねすると_d_bに保存され元の画面へリダイレクト(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->from("/books/{$book->id}")->post("/reviews/{$review->id}/like");

        $response->assertStatus(302)->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseHas('like_review', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    /**
     * ログインユーザーが再度いいねボタンを押下した場合、DBから削除され元の画面へリダイレクトされるか検証　302 Found
     */
    public function test_ログインユーザーがいいねを解除すると_d_bから削除され元の画面へリダイレクト(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        DB::table('like_review')->insert([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $response = $this->actingAs($user)
            ->from("/books/{$book->id}")
            ->post("/reviews/{$review->id}/like");

        $response->assertStatus(302)->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseMissing('like_review', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    /**
     * 未ログインユーザーがいいねボタンを押下した場合、ログイン画面へリダイレクトされるか検証　302 Found
     */
    public function test_未ログインユーザーがいいねを押すとログイン画面へリダイレクトする(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->post("/reviews/{$review->id}/like");

        $response->assertStatus(302)->assertRedirect('/login');
    }
}
