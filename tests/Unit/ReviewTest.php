<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * bookリレーションが正しい型とモデルを返すか検証
     */
    public function test_bookリレーションが正しい型とモデルを返すか(): void
    {
        $review = Review::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $review->book());
        $this->assertInstanceOf(Book::class, $review->book);
    }

    /**
     * userリレーションが正しい型とモデルを返すか検証
     */
    public function test_userリレーションが正しい型とモデルを返すか(): void
    {
        $review = Review::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $review->user());
        $this->assertInstanceOf(User::class, $review->user);
    }

    /**
     * reviewの属性が正しく保持取得できるか検証
     */
    public function test_reviewの属性が正しく保持取得できるか(): void
    {
        $review = Review::factory()->make([
            'rating' => 5,
            'comment' => '素晴らしい本でした！',
        ]);

        $this->assertSame(5, $review->rating);
        $this->assertSame('素晴らしい本でした！', $review->comment);
    }
}
