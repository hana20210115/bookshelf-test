<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * ユーザーがレビューを更新できるか
     */
    public function update(User $user, Review $review): bool
    {
        // ログイン中のユーザーIDと、レビューの投稿者IDが一致していればtrue
        return $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
}
