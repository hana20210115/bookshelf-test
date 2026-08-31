<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        // 評価別の日本語定型コメントテンプレート
        $comments = [
            1 => 'あまり参考になりませんでした。',
            2 => '少し内容が難しかったです。',
            3 => '普通の内容でした。',
            4 => 'とても分かりやすく、参考になりました！',
            5 => '最高の一冊でした！何度も読み返します。',
        ];

        foreach ($books as $book) {
            // 各書籍に対して 2〜4 件のレビューを作成
            $reviewCount = rand(2, 4);

            // 【重要】同一書籍内でユーザーが重複しないよう、ユーザーをシャッフルして必要な人数だけ抽出
            $reviewers = $users->shuffle()->take($reviewCount);

            foreach ($reviewers as $reviewer) {
                $rating = rand(1, 5);

                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $reviewer->id,
                    'rating' => $rating,
                    'comment' => $comments[$rating],
                ]);
            }
        }
    }
}
