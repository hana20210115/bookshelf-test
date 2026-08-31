<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;

class SanctumApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未認証アクセス：APIトークンを持たない場合の検証
     * @return void
     */
    public function test_APIトークンを持たずにアクセスした場合401Unauthorizedが返ること(): void
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => 'トークンなしの登録テスト',
            'author' => 'テスト著者',
        ]);

        $response->assertStatus(401);
    }

    /**
     * 他ユーザーデータ操作：有効なAPIトークン保持時でも他人のリソースを操作できないかの検証
     * @return void
     */
    public function test_有効なAPIトークンでも他人のデータを操作した場合は403Forbiddenが返ること(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $book = Book::factory()->create(['user_id' => $owner->id]);


        $genre = Genre::factory()->create();

        $response = $this->actingAs($otherUser, 'sanctum')->putJson("/api/v1/books/{$book->id}", [
            'title' => '勝手にタイトル変更',
            'author' => '勝手に著者変更',
            'genres' => [$genre->id], 
        ]);

        
        $response->assertStatus(403);
    }
}