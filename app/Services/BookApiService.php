<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class BookApiService
{
    /**
     * 一覧取得
     */
    public function getList($request): LengthAwarePaginator
    {
        $query = Book::with('genres')->with('genres')->withAvg('reviews', 'rating')->withCount('reviews');

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%'.$request->keyword.'%')
                ->orWhere('author', 'like', '%'.$request->keyword.'%');
        }

        return $query->paginate(10);
    }

    /**
     * 新規登録
     *
     * @param  int  $userId
     */
    public function create(array $data): Book
    {
        // $dataの配列の中からgenresキーを取り除く（元のデータはそのまま）
        $bookData = Arr::except($data, ['genres']);

        // sanctumの認証機能は応用編なのでuser_id=1にしておく
        $bookData['user_id'] = 1;

        // 書籍を保存
        $book = Book::create($bookData);

        // ジャンルを中間テーブルに紐付け、Requestで空のジャンルは入って来ないようになっているが、一応issetメソッドを使う
        if (isset($data['genres'])) {
            $book->genres()->sync($data['genres']);
        }

        return $book;
    }

    /**
     * 更新
     */
    public function update(Book $book, array $data): Book
    {
        $bookData = Arr::except($data, ['genres']);

        $book->update($bookData);

        if (isset($data['genres'])) {
            $book->genres()->sync($data['genres']);
        }

        return $book;
    }
}
