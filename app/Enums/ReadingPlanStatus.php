<?php

namespace App\Enums;

enum ReadingPlanStatus:string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case OVERDUE = 'overdue';

    public const Completed = self::COMPLETED;
    public const InProgress = self::IN_PROGRESS;
    public const Overdue = self::OVERDUE;

    /**
     * 画面表示用の日本語ラベルを取得する
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::IN_PROGRESS => '進行中',
            self::COMPLETED => '読了',
            self::OVERDUE => '期限超過',
        };
    }

    /**
     * 画面表示用のCSS（色）を取得する
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::IN_PROGRESS => 'bg-blue-100 text-blue-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::OVERDUE => 'bg-red-100 text-red-800',
        };
    }
}