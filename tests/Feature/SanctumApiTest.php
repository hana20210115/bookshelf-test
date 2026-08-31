<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未認証アクセス：APIトークンを持たない場合の検証
     */
    public function test_ap_iトークンを持たずにアクセスした場合401_unauthorizedが返ること(): void
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => 'トークンなしの登録テスト',
            'author' => 'テスト著者',
        ]);

        $response->assertStatus(401);
    }

    /**
     * 他ユーザーデータ操作：有効なAPIトークン保持時でも他人のリソースを操作できないかの検証
     */
    public function test_有効な_ap_iトークンでも他人のデータを操作した場合は403_forbiddenが返ること(): void
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
