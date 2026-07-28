<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class RankingService
{
    /**
     * レビュー平均評価TOP10の書籍を取得する
     */
    public function getTopRatedBooks(): Collection
    {
        return Book::has('reviews')// レビューが付いている書籍を絞り込む
            ->withAvg('reviews', 'rating')// Bookモデルのreviewsメソッドでレビューのrating(星の数)の平均を取得
            ->orderByDesc('reviews_avg_rating')// 星の平均を降順で並び替える
            ->take(10)// 上から10件取得
            ->get();
    }
}
