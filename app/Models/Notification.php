<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * 通知を所有するユーザーを取得する
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDataAttribute()
    {
        return [
            'title' => '読書計画リマインダー',    // Bladeの data['title'] に渡される
            'body' => $this->message,            // データベースの message カラムの中身を data['body'] として渡す
            'timing' => 'three_days_before',       // カレンダーのアイコン（青色）を表示させるための指定
        ];
    }
}
