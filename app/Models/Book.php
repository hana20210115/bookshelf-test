<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'description',
        'isbn',
        'published_date',
        'image_url',
    ];

    protected $casts = [
        'published_date' => 'date',
    ];

    /**
     * この書籍を登録したユーザー（1対1)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この書籍に紐づくジャンル（多対多）
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * この書籍に対するレビュー(1対多)
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * この書籍をお気に入り登録しているユーザー（多対多）
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(user::class, 'favorites');
    }

    /**
     * この書籍に紐づく読書計画（1対多）
     */
    public function readingPlans(): HasMany
    {
        return $this->HasMany(ReadingPlan::class);
    }
}
