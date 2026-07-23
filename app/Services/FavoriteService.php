<?php
namespace App\Services;

use App\Models\Book;
use App\Models\User;

class FavoriteService
{
    /**
     * お気に入りの追加・解除を切り替える（トグル）
     * 
     * @param Book $book
     * @param User $user
     * @return void
     * 
     */
    public function toggleFavorite(Book $book, User $user):void
    {
        $book->favorites()->toggle($user->id);
    }

}