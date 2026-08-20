<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class FavoriteService
{
    /**
     * お気に入りの追加・解除を切り替える（トグル）
     * @param Book $book
     * @param User $user
     * @return void
     */
    public function toggleFavorite(Book $book, User $user): void
    {
        $book->favorites()->toggle($user->id);
    }

    /**
     * ユーザーのお気に入り書籍をページネーションで取得する
     * @param USer $user
     * @return LengthAwarePaginator
     */
    public function getFavoriteBooks(User $user): LengthAwarePaginator
    {
        return $user->favoriteBooks()->orderBy('favorites.created_at', 'desc')->paginate(10);
    }
}
