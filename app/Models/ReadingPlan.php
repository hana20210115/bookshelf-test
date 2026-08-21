<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\RedingPlansStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ReadingPlanStatus;

class ReadingPlan extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'book_id',
        'target_date',
        'status',
        'completed_at',
    ];

    Protected $casts = [
        'target_date' => 'date',
        'status' => ReadingPlanStatus::class,
        'completed_at' => 'datetime',
    ];

    /**
     * この読書計画を作成したユーザー（多対1）
     * @return BelongsTo
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この読書計画の対象となる書籍（多対1）
     * @return BelongsTo
     */
    public function book():BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

}
