<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Book;
use App\models\User;
use Illuminate\Database\Seeder;

/**
 * レビューのテストデータを投入するシーダークラス
 */

class ReviewSeeder extends Seeder
{
    /**
     * データベースにデータを投入する
     * 
     * @return void
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $comments =[
            'とても素晴らしい本でした。何度でも読み返したいです。',
            '考えさせられる内容が多く、今後の人生に活かせそうです。',
            '内容はいいのですが、少し難しい部分がありました。',
            '期待通りの内容で満足です。友達にも勧めたいです。',
            '最初は退屈でしたが、後半から一気に面白くなりました。'
        ];

        foreach($books as $book){
            $reviewCount = rand(2,4);//2~4の数字を生成
            $reviewers = $users->random($reviewCount);//ランダムで選ばれたユーザーを取得

            //上で選ばれた2〜4人のユーザーを１人ずつ取り出して処理している
            foreach($reviewers as $user ){
                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => rand(3,5),//ランダムで3〜５の評価をしている
                    'comment' => $comments[array_rand($comments)],//array_randメソッドでランダムでインデックス番号を取り出している。
                ]);
            }
        }

    }
}
