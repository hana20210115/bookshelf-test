<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーが自分が登録した書籍を編集した場合、(DB.books_table)が更新され、書籍詳細画面へリダイレクトするか検証　302 Found
     */
    public function test_ログインユーザーが自分が登録した書籍を編集した場合_d_bが更新され書籍詳細画面へリダイレクトされる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $updateData = [
            'title' => '更新したタイトル',
            'author' => '更新した著者',
            'description' => '更新した説明文',
            'isbn' => '2222222222222',
            'genres' => [$genre->id],
            'published_date' => '2025-01-01',
        ];

        $response = $this->actingAs($user)->put("/books/{$book->id}", $updateData);

        $response->assertStatus(302)->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新したタイトル',
            'author' => '更新した著者',
            'description' => '更新した説明文',
            'isbn' => '2222222222222',
            'published_date' => '2025-01-01',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);

    }

    /**
     * ログインユーザーが、他人が登録した書籍の更新処理を行なった場合、４０３エラーが表示時されるか検証 403 Forbidden
     */
    public function test_他人が登録した書籍を更新しようとした場合403エラーが表示される(): void
    {
        $loginUser = User::factory()->create();
        $otherUser = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $updateData = [
            'title' => 'ニュータイトル',
            'author' => 'ニュー著者',
            'genres' => [$genre->id],
            'description' => 'ニュー説明文',
            'isbn' => '0000000000000',
            'published_date' => '2026-01-01',
        ];

        $response = $this->actingAs($loginUser)->put("/books/{$book->id}", $updateData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('books', [
            'title' => 'ニュータイトル',
            'author' => 'ニュー著者',
            'description' => 'ニュー説明文',
            'isbn' => '0000000000000',
            'published_date' => '2026-01-01',
        ]);
    }
}
