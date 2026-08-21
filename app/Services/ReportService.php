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
    public function getSummary(int $userId): array
    {
        $totalReviews = Review::where('user_id', $userId)->count();
        $averageRating = Review::where('user_id', $userId)->avg('rating');

        $completedBooksCount = ReadingPlan::where('user_id', $userId)
            ->where('status', ReadingPlanStatus::COMPLETED)
            ->distinct('book_id')
            ->count('book_id');

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating ? round((float)$averageRating, 1) : 0.0,
            'completed_books_count' => $completedBooksCount,
        ];
    }

    /**
     * 高評価書籍TOP５を取得する
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getTopRatedBooks(int $userId, int $limit = 5): array
    {
        $reviews = Review::with('book')
            ->where('user_id', $userId)
            ->where('rating', '>=', 4)
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $reviews->map(function ($review) {
            return [
                'id' => $review->book ? $review->book->id : 0,
                'title' => $review->book ? $review->book->title : '不明な書籍',
                'author' => $review->book ? $review->book->author : '',
                'rating' => $review->rating,
            ];
        })->toArray();
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
        return Review::with('book.genres')
            ->where('user_id', $userId)
            ->get()
            ->flatMap(function ($review) {
                $genres = $review->book?->genres;

                if (!$genres || $genres->isEmpty()) {
                    return collect([[
                        'id' => 0,
                        'name' => '未分類',
                        'rating' => $review->rating,
                    ]]);
                }

                return $genres->map(fn($genre) => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'rating' => $review->rating,
                ]);
            })
            ->groupBy('id')
            ->map(fn($group) => [
                'id' => $group->first()['id'],
                'name' => $group->first()['name'],
                'average_rating' => round((float) $group->avg('rating'), 1),
                'count' => $group->count(),
            ])
            ->sortByDesc('average_rating')
            ->take($limit)
            ->values();
    }

    /**
     * 評価分布（星1〜5の件数）を取得する
     * 
     * @param int $userId
     * @return SupportCollection
     */
    public function getRatingDistribution(int $userId): SupportCollection
    {
        $distribution = collect([0, 0, 0, 0, 0]);

        $counts = Review::where('user_id', $userId)
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        foreach ($counts as $rating => $count) {
            $distribution[$rating - 1] = $count;
        }

        return $distribution;
    }
}