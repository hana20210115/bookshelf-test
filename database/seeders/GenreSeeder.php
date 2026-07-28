<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

/**
 * ジャンルのデータを投入するクラス
 */
class GenreSeeder extends Seeder
{
    /**
     * データベースへデータ投入を実行する
     *
     * return void
     */
    public function run(): void
    {
        $genres = [
            '小説', 'ビジネス', '技術書', '自己啓発', 'エッセイ', '歴史', '科学', '芸術', '料理', '旅行'];

        // firstOrCreateメソッドでデータが重複しないようにしている
        foreach ($genres as $genre) {
            Genre::firstOrCreate(['name' => $genre]);
        }

    }
}
