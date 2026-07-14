<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'comment',
        'rating',

    ]

    /**
     * レビュー対象の書籍(1対1)
     */
    public function book():BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * レビューを投稿したユーザー(1対1)
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このレビューにいいねしたユーザー(多対多)
     */
    public function likedUsers():belongsToMany
    {
        return $this->belongsYoMany(User::class);
    }

}
