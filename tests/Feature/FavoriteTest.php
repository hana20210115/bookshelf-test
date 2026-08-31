<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーが書籍お気に入りボタンを押下した場合、(DB.favorites_table)に登録され元の画面へリダイレクトされるか検証　302 Found
     */
    public function test_ログインユーザーがお気に入り追加すると_d_bに保存され元の画面へリダイレクトする(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // fromメソッドでこのURLからボタンを押した状態を作れる
        $response = $this->actingAs($user)
            ->from("/books/{$book->id}")
            ->post("books/{$book->id}/favorites");

        $response->assertStatus(302)->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /**
     * ログインユーザーが再度お気に入りボタンを押下した場合、(DB.favorites_table)のレコードが削除され元の画面へリダイレクト 302 Found
     */
    public function test_ログインユーザーがお気に入り解除すると_d_bから削除され元の画面へリダイレクト(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->from("/books/{$book->id}")
            ->post("/books/{$book->id}/favorites");

        $response->assertStatus(302)->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /**
     * 未ログインユーザーが書籍お気に入りボタンを押下した場合、ログイン画面にリダイレクトされるか検証　302 Found
     */
    public function test_未ログインユーザがお気に入りボタンを押下した場合ログイン画面へリダイレクトする(): void
    {
        $book = Book::factory()->create();

        $response = $this->post("books/{$book->id}/favorites");

        $response->assertStatus(302)->assertRedirect('/login');
    }
}
