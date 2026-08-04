<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 書籍登録時にジャンルを選択して送信した場合、中間テーブル(DB.book_genre_table)にbook_id,genre_idが保存され、書類作成画面にリダイレクト 302 Found
     * @return void
     */
    public function test_書籍登録時にジャンルを選択した場合中間テーブルに保存される():void
    {
        $user = User::factory()->create();

        $genre1 = Genre::factory()->create(['name' => 'テストジャンル1']);
        $genre2 = Genre::factory()->create(['name' => 'テストジャンル２']);

        $bookData = [
            'title' => 'テスト本',
            'author' => 'テスト著者',
            'description' => 'テスト説明文',
            'isbn' => '1111111111111',
            'published_date' => '2026-01-01',
            'genres' => [$genre1->id,$genre2->id],
        ];

        $response = $this->actingAs($user)->post('/books',$bookData);

        $response->assertStatus(302)->assertRedirect('books/create');

        //登録された書籍を取り出す
        $book = Book::where('title','テスト本')->first();

        //中間テーブルに、書籍とジャンルのIDが正しく保存されているか確認
        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre1->id,
        ]);
        
        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre2->id,
        ]);


    }
}
