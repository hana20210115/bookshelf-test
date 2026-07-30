<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
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
            'name'=>'テストジャンル',
        ];
    }
}
