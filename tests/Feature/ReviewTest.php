<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーが正しい値でレビューを投稿した場合、(DB.review_table)に保存され、該当書籍詳細画面へリダイレクト　302 Found
     * @return void
     */
    public function test_ログインユーザーがレビューを投稿するとDBに保存され詳細画面へリダイレクト():void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $reviewData = [
            'book_id' => $book->id,
            'rating' => 5,
            'comment' =>'とても面白い内容です',
        ];

        $response = $this->actingAs($user)->post("/books/{$book->id}/reviews",$reviewData);

        $response->assertStatus(302)->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseHas('reviews',[
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' =>'とても面白い内容です',
        ]);
    }
    
    /**
     * 未ログインユーザーがレビューを投稿した場合、ログイン画面へリダイレクト　302 Found
     * @return void
     */
    public function test_未ログインユーザーがレビューを投稿した場合ログイン画面へリダイレクト():void
    {
        $book = Book::factory()->create();

        $reviewData = [
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => '未ログインで投稿',
        ];

        $response = $this->post("books/{$book->id}/reviews",$reviewData);

        $response->assertStatus(302)->assertRedirect('/login');
    }
}
