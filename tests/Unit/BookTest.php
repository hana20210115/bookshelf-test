<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * reviewsリレーションが正しい型とモデルを返すかを検証
     */
    public function test_reviewsリレーションが正しい型とモデルを返すか(): void
    {
        $book = Book::factory()->create();
        Review::factory()->create(['book_id' => $book->id]);

        $this->assertInstanceOf(HasMany::class, $book->reviews());
        $this->assertInstanceOf(Review::class, $book->reviews->first());
    }

    /**
     * genresリレーションが正しい型とモデルを返すかを検証
     */
    public function test_genresリレーションが正しい型とモデルを返すか(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();
        $book->genres()->attach($genre->id);

        $this->assertInstanceOf(BelongsToMany::class, $book->genres());

        $this->assertInstanceOf(Genre::class, $book->genres->first());
    }
}
