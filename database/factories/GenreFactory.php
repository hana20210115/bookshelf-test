<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * 機能テストで使うジャンルデータを生成
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'テストジャンル',
        ];
    }
}
