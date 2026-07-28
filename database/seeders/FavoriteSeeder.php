<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * お気に入りのテストデータを投入するシーダークラス
 */
class FavoriteSeeder extends Seeder
{
    /**
     * データベースへのデータを投入を実行する
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            // 各ユーザーに3〜５冊のお気に入りを設定
            $favoriteBooks = $books->random(rand(3, 5))->pluck('id')->toArray();

            // Userモデルのリレーション（favoriteBooks)を使用してsync
            $user->favoriteBooks()->syncWithoutDetaching($favoriteBooks); // syncWithoutDetachingを使う場合、シーダーなどで「エラーを出さずに、安全にデータを付け足したい時」に使う。
        }
    }
}
