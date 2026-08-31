<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;

class ApiBookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * /api/v1/booksにアクセスした際、JSONデータが返ってくる　200 OK
     * @return void
     */
    public function test_APIで書籍一覧を取得すると200OKとJSONが返ってくる():void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * POST処理で正しいデータを送信した場合、（DB.books_table)に保存されるか検証　201 created
     * @return void
     */
    public function test_APIで正しいデータを送信するとDBに保存され201が返る():void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $bookData = [
            'title' => 'APIタイトル',
            'author' => 'API著者',
            'genres' => [$genre->id],
            'description' => 'テスト説明文',
            'isbn' => '5555555555555',
            'published_date' => '2026-01-01',
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/books',$bookData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('books',[
            'title' => 'APIタイトル',
            'author' => 'API著者',
            'description' => 'テスト説明文',
            'isbn' => '5555555555555',
            'published_date' => '2026-01-01',
            'user_id' => $user->id,
        ]);
    }

    /**
     * PUT処理で正しいデータを送信した場合、（DB.books_table)が更新される
     * @return void
     */
    public function test_APIで正しいデータを送信するとDBが更新され200が返る(): void
    {

        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'title' => '古いタイトル',
            'author' => '古い著者',
        ]);


        $updateData = [
            'title' => '更新後のAPIタイトル',
            'author' => '更新後のAPI著者',
            'genres' => [$genre->id], // これも先ほどの教訓を活かして配列で！
            'description' => '更新後のテスト説明文',
            'isbn' => '9999999999999',
            'published_date' => '2026-12-31',
            'user_id' => $user->id,
        ];


        $response = $this->actingAs($user)->putJson("/api/v1/books/{$book->id}", $updateData);


        $response->assertStatus(200);

        $this->assertDatabaseHas('books', [
            'id' => $book->id, // 対象の本のIDを指定
            'title' => '更新後のAPIタイトル',
            'author' => '更新後のAPI著者',
            'description' => '更新後のテスト説明文',
            'isbn' => '9999999999999',
            'published_date' => '2026-12-31',
            'user_id' => $user->id,
        ]);
    }

    /**
     * DELETE処理を行なった場合、削除処理が成功するか検証　204 No Content
     * @return void
     */
    public function test_APIで削除処理を行うと成功して204が返る():void
    {
        $user = User::factory()->create();
        

        $book = Book::factory()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user, 'sanctum')->deleteJson("api/v1/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books',[
            'id' => $book->id,
        ]);
    }

    /**
     * POST,PUT処理でバリデーションエラーになるデータを送信した場合、エラーJSONが返ってくるか検証　422 Unprocessable Entity
     * @return void 
     */
    public function test_APIで不正なデータを送信すると422バリデーションエラーが返る():void
    {
        $user = User::factory()->create();


        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/books', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'title' => 'タイトルを入力してください',
            'author' => '著者を入力してください',
            // 他に必須項目のメッセージがあればここに合わせて記載
        ]);
    }

}
