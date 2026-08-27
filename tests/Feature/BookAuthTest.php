<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;

class BookAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーが書籍編集、書籍削除を行なった場合ログイン画面にリダイレクトされるか検証
     * @return void
     */
    public function test_未ログインユーザーがアクセスした場合ログイン画面へリダイレクトされるか():void
    {
        $book = Book::factory()->create();

        $this->get("/books/{$book->id}/edit")->assertRedirect('/login');

        $this->put("/books/{$book->id}",[])->assertRedirect('/login');

        $this->delete("/books/{$book->id}/delete")->assertRedirect('login');
    }

    /**
     * 対象の書籍を登録したユーザー以外がアクセスした場合403エラーが返るか検証　Forbidden
     * @return void
     */
    public function test_対象の書籍を作成したユーザー以外がアクセスした場合403が返るか():void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id
        ]);

        $updateData = [
        'title' => 'テストタイトル',
        'author' => 'テスト著者',
        'isbn' => '9784798177286',
        'published_date' => '2023-01-01',
        'description' => 'テスト説明',
        'genres' => [$genre->id],
        ];

        //編集画面へ遷移
        $this->actingAs($otherUser)
            ->get("/books/{$book->id}/edit")
            ->assertStatus(403);

        //更新処理
        $this->actingAs($otherUser)
            ->put("/books/{$book->id}",$updateData)
            ->assertStatus(403);

        //削除処理
        $this->actingAs($otherUser)
        ->delete("/books/{$book->id}/delete")
        ->assertStatus(403);

        $this->assertDatabaseHas('books',[
            'id' => $book->id,
            'title' => $book->title,
        ]);
    }
}