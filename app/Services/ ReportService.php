<?php

namespace App\Services;

use App\Models\Review;
use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;

class ReportService
{
    /**
     * 基本サマリー（総レビュー数、平均評価点、読了冊数）を取得
     * @param int $userId
     * @return array
     */
    public function getSummary(int $userId):array
    {
        $totalReviews = Review::where('user_id',$userId)->count();
        $averageRating = Review::where('user_id',$userId)->avg('rating');

        $completedBooksCount = ReadingPlan::where('user_id',$userId)
        ->where('status',ReadingPlanStatus::COMPLETED)
        ->distinct('book_id')
        ->count('book_id');
        //distinctメソッドでダブりはカウントしないようにしている

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating ? round((float)$averageRating,1):0.0,
            'completed_books_count' => $completedBooksCount,
        ];


    }

    /**
     * 高評価書籍TOP５を取得する（評価4以上、降順、同点時は作成日時の新しい順）
     * 
     * @param int $userId
     * @param int $limit
     * @return EloquentCollection
     */
    public function getTopRatedBooks(int $userId,int $limit = 5):EloquentCollection
    {
        return Review::with('book')
            ->where('user_id',$userId)
            ->where('rating','>=',4)
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * ジャンル別評価傾向TOP５を取得する（平均評価の降順）
     * 
     * @param int $userId
     * @param int $limit
     * @return SupportCollection
     */
    public function getTopGenres(int $userId, int $limit = 5): SupportCollection
    {
        $reviews = Review::with('book.genre')
            ->where('user_id',$userId)
            ->get();

        //groupByコレクションメソッドで$reviewsに入っているレビューを一つづつ取り出して、ジャンル名をキーにしてグループにしている
        $genres = $reviews->groupBy(function ($review){
            return $review->book && $review->book->genre ? $review->book->genre->name : '未分類';
        })

        ->map(function($group,$genreName){
            return (object)[
                'name' => $genreName,
                'average_rating' => round((float)$group->avg('rating'),1),
            ];
        });

        return $genres->sortByDesc('average_rating')->take($limit)->values();
    }
}