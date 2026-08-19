<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\models\User;
use Illuminate\Database\Seeder;

/**
 * レビューのテストデータを投入するシーダークラス
 */
class ReviewSeeder extends Seeder
{
    /**
     * データベースにデータを投入する
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $comments = [
            1 => '期待はずれでした',
            2 => '少し難しい内容でした',
            3 => '普通のです',
            4 => 'とても良かったです',
            5 => '最高の良書です',
        ];

        foreach ($books as $book) {
            $reviewCount = rand(2, 4); // 2~4の数字を生成
            $reviewers = $users->random($reviewCount); // ランダムで選ばれたユーザーを取得

            // 上で選ばれたランダムユーザーを１人ずつ取り出して処理している
            foreach ($reviewers as $user) {

                $rating = rand(1,5);

                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => $rating, // ランダムで1〜５の評価をしている
                    'comment' => $comments[$rating], // 評価の数字と同じキーのコメントをしている
                ]);
            }
        }

    }
}
