<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
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
