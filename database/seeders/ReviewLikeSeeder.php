<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * レビューへのいいねのテストデータを投入するシーデークラス
 */
class ReviewLikeSeeder extends Seeder
{
    /**
     * データベースへデータを投入する
     * @return void
     */
    public function run(): void
    {
        $reviews = Review::all();
        $users = User::all();

        foreach($reviews as $review){
            //各レビューに0〜3人のユーザーがいいね
            $likeCount = rand(0,3);

            if ($likeCount > 0){
                //自分のレビューを省いたユーザーからランダムに取得
                $potentialLikers = $users -> where('id','!=',$review->user_id);
                
                $likerIds = $potentialLikers->random(min($likeCount, $potentialLikers->count()))->pluck('id')->toArray();

                // Reviewモデルのリレーション(liked ByUsers)を使用してsync
                $review->likedByUsers()->syncWithoutDetaching($likerIds);
            }
        }
    }
}
