<?php

namespace Tests\Feature;

use App\models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IsbnSearchEndpointTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /**
     * 正しいISBNを送信した場合JSON形式で書籍情報が返るか検証
     */
    public function test_正しい_isb_nを送信した場合_jso_n形式で書籍情報が返るか(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'www.googleapis.com/*' => Http::response([
                'totalItems' => 1,
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'テストAPI書籍',
                            'authors' => ['テスト著者'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->getJson('/books/isbn/9781234567890');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'テストAPI書籍']);
    }

    /**
     * 不正なisbnが送信された場合Serviceを呼ばずに422エラーが返るか検証
     */
    public function test_不正な_isb_nが送信された場合_serviceを呼ばずに422エラーが返ること(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/books/isbn/123');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['isbn']);
    }
}
