<?php

namespace App\Service;

use App\Models\ReadingPlan;
use App\Models\Book;
use App\Enums\ReadingPlanStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReadingPlanService
{
    /**
     * 指定したユーザーの読書計画一覧をページネーションで取得する
     * @param  int $userId
     * @param nullable|string $status
     * @param  int $perPage
     * @return LengthAwarePaginator
     */
    public function getReadingPlans(int $userId, ?string $status, int $perPage = 10):LengthAwarePaginator
    {
        $query = ReadingPlan::where('user_id',$userId)->with('book');

        if($status){
            $query->where('status',$status);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * 登録可能な書籍一覧を取得する
     * 
     * @return Collection
     */
    public function getAllBooks(): Collection
    {
        return Book::all();
    }

    /**
     * 読書計画を新規作成する
     * 
     * @param  int $userId
     * @param  array $data
     * @return ReadingPlan
     */
    public function createReadingPlan(int $userId, array $data):ReadingPlan
    {
        return ReadingPlan::create([
            'user_id' => $userId,
            'book_id' => $data['book_id'],
            'target_date' => $data['target_date'],
        ]);
    }

    /**
     * 読書計画を更新する
     * 
     * @param ReadingPlan $readingPlan
     * @param array $data
     * @return bool
     */
    public function updateReadingPlan(ReadingPlan $readingPlan,array $data):bool
    {
        $status = $readingPlan->status;

        if($status === ReadingPlanStatus::OVERDUE){
            $status = ReadingPlanStatus::IN_PROGRESS;
        }

        return $readingPlan->update([
            'target_date' => $data['target_date'],
            'status' => $status,
        ]);
    }

    /**
     * 読書計画を削除する
     * 
     * @param ReadingPlan $readingPlan
     * @return bool|null
     */
    public function deleteReadingPlan(ReadingPlan $readingPlan):?bool
    {
        return $readingPlan->delete();
    }

    /**
     * 読書計画を読了ステータスに更新する
     * 
     * @param ReadingPlan $readingPlan
     * @return bool
     */
    public function completeReadingPlan(ReadingPlan $readingPlan):bool
    {
        return $readingPlan->update([
            'status' => ReadingPlanStatus::COMPLETED,
        ]);
    }


}