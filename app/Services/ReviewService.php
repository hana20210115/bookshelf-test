<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Review;

class ReviewService
{
    /**
     * レビューの登録処理
     */
    public function storeReview(Book $book, array $validatedData, int $userId): Review
    {
        return $book->reviews()->create([
            'user_id' => $userId,
            'rating' => $validatedData['rating'],
            'comment' => $validatedData['comment'],
        ]);
    }

    /**
     * 既存レビューの更新処理
     *
     * @param array
     */
    public function updateReview(Review $review, array $validatedData): bool
    {
        return $review->update($validatedData);
    }

    /**
     * レビューの削除処理
     */
    public function deleteReview(Review $review): void
    {
        // 関連するいいねを解除してから削除
        $review->likedByUsers()->detach();
        $review->delete();
    }

    /**
     * レビューに対する「いいね」の切り替え（追加/解除）処理
     */
    public function toggleLike(Review $review, int $userId): array
    {
        return $review->likedByUsers()->toggle($userId);
    }
}
