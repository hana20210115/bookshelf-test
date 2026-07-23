<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    /**
     * ユーザーが本を編集できるかの判定
     */
    public function update(User $user,Book $book): bool
    {   //ログインユーザーと本に紐づいているUser_idが同じならtrue、違ったらfalse
        return $user->id === $book->user_id;
    }

    /**
     * ユーザーが本を削除できるかの判定
     */
    public function Delete(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }

    

    

    
}
