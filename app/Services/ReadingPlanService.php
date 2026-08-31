<?php

namespace App\Services;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReadingPlanService
{
    /**
     * 指定したユーザーの読書計画一覧をページネーションで取得する
     *
     * @param  nullable|string  $status
     */
    public function getReadingPlans(int $userId, ?string $status, int $perPage = 10): LengthAwarePaginator
    {
        $query = ReadingPlan::where('user_id', $userId)->with('book');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * 登録可能な書籍一覧を取得する
     */
    public function getAllBooks(): Collection
    {
        return Book::all();
    }

    /**
     * 読書計画を新規作成する
     */
    public function createReadingPlan(int $userId, array $data): ReadingPlan
    {
        return ReadingPlan::create([
            'user_id' => $userId,
            'book_id' => $data['book_id'],
            'target_date' => $data['target_date'],
        ]);
    }

    /**
     * 読書計画を更新する
     */
    public function updateReadingPlan(ReadingPlan $readingPlan, array $data): bool
    {
        $status = $readingPlan->status;

        if ($status === ReadingPlanStatus::OVERDUE) {
            $status = ReadingPlanStatus::IN_PROGRESS;
        }

        return $readingPlan->update([
            'target_date' => $data['target_date'],
            'status' => $status,
        ]);
    }

    /**
     * 読書計画を削除する
     */
    public function deleteReadingPlan(ReadingPlan $readingPlan): ?bool
    {
        return $readingPlan->delete();
    }

    /**
     * 読書計画を読了ステータスに更新する
     */
    public function completeReadingPlan(ReadingPlan $readingPlan): bool
    {
        return $readingPlan->update([
            'status' => ReadingPlanStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }
}
