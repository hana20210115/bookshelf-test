<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;

class AccessTest extends TestCase
{
    use RefreshDatabase;//テスト完了時データベースをリセットする

    /**
     * ゲストが書籍一覧、書籍詳細、ランキング画面にアクセスできるか　（200 OK）
     * @return void
     */
    public function test_ゲストが書籍一覧詳細ランキング画面にアクセスできるか():void
    {
        $book = Book::factory()->create();

        $this->get('/books')->assertStatus(200);
        $this->get("/books/{$book->id}")->assertStatus(200);
        $this->get('/ranking')->assertStatus(200);

    }

    /**
     * ゲストがログインユーザー用画面にアクセスした場合、ログイン画面へリダイレクトする　(302 Found）
     * @return void
     */
    public function test_ゲストがログインユーザー用画面にアクセスした場合ログイン画面へリダイレクト():void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();
        $review = Review::factory()->create();

        $protectedUrls = [
            '/books/create',
            "/books/{$book->id}/edit",
            '/genres',
            "/genres/{$genre->id}/edit",
            "/reviews/{$review->id}/edit",
            '/favorites',
        ];

        foreach ($protectedUrls as $url){
            $this->get($url)
            ->assertStatus(302)
            ->assertRedirect('/login');
        }
    }

    /**
     * ログインユーザーがログインユーザー用の画面にアクセスできるか　(200 ok)
     * @return void
     */
    public function test_ログインユーザーがログインユーザー専用画面にアクセスできるか():void 
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $genre = Genre::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id,
        'book_id' => $book->id]);

        $protectedUrls = [
            '/books/create',
            "/books/{$book->id}/edit",
            '/genres',
            "/genres/{$genre->id}/edit",
            "/reviews/{$review->id}/edit",
            '/favorites',
        ];

        foreach ($protectedUrls as $url){
            $this->actingAs($user)
            ->get($url)
            ->assertStatus(200);
        }
    }
}
