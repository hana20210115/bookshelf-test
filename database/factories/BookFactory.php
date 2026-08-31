<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * 機能テストで使う書籍データの素材を生成
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->realText(20),
            'author' => fake()->name(),
            'isbn' => fake()->isbn13(),
            'description' => fake()->realText(50),
            'published_date' => fake()->date(),
            'user_id' => User::factory(),
        ];
    }
}
