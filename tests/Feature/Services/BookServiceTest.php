<?php

namespace Tests\Feature\Services;

use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookService $bookService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookService = new BookService;
    }

    /**
     * 正常時200のステータスで意図した構造の配列データが変えるか検証
     */
    public function test_正常時200に意図した構造の配列のデータが返ること(): void
    {
        Http::fake([
            'www.googleapis.com/*' => Http::response([
                'totalItems' => 1,
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'モック書籍タイトル',
                            'authors' => ['モック 太郎', 'モック 次郎'],
                            'publishedDate' => '2026-08-26',
                            'description' => 'モック用の説明文です。',
                            'imageLinks' => [
                                'thumbnail' => 'http://example.com/test.jpg',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = $this->bookService->getBookInfoByIsbn('9781234567890');

        $this->assertTrue($result['is_success']);
        $this->assertTrue($result['is_success']);
        $this->assertEquals(200, $result['status']);
        $this->assertEquals('モック書籍タイトル', $result['data']['title']);
        // 複数の著者が implodeメソッドで正しくカンマ区切りになるか検証
        $this->assertEquals('モック 太郎, モック 次郎', $result['data']['author']);
        $this->assertEquals('http://example.com/test.jpg', $result['data']['image_url']);
    }

    /**
     * 該当なしステータスが404の時、意図したメッセージが返るか検証
     */
    public function test_該当なしステータス404の時に意図したメッセージが返るか(): void
    {
        Http::fake([
            'www.googleapis.com/*' => Http::response([
                'totalItems' => 0,
            ], 200),
        ]);

        $result = $this->bookService->getBookInfoByIsbn('9780000000000');

        $this->assertFalse($result['is_success']);
        $this->assertEquals(404, $result['status']);
        $this->assertEquals('書籍情報が見つかりませんでした。', $result['message']);
    }

    /**
     * 通信制限時ステータス429のときに意図したメッセージが返るか検証
     */
    public function test_通信制限時_429_のときに意図したステータスとメッセージが返ること(): void
    {
        Http::fake([
            'www.googleapis.com/*' => Http::response([], 429),
        ]);

        $result = $this->bookService->getBookInfoByIsbn('9781234567890');

        $this->assertFalse($result['is_success']);
        $this->assertEquals(429, $result['status']);
        $this->assertEquals('アクセスが集中しています。しばらく経ってから再度お試しください。', $result['message']);
    }
}
