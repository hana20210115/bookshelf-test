<?php

namespace App\Services;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GenreService
{
    /**
     * 書籍数付きの全ジャンルの取得
     * @return Collection
     */
    public function getGenresWithBookCount(): Collection
    {
        return Genre::withCount('books')->get();
    }

    /**
     * 特定のジャンルに紐づく書籍をページネーションで取得
     * @param Genre $genre
     * @return LengthAwarePaginator
     */
    public function getPaginatedBooksGenre(Genre $genre): LengthAwarePaginator
    {
        return $genre->books()->paginate(10);
    }

    /**
     * ジャンルを新規登録
     * @param array $validatedData
     * @return Genre
     */
    public function storeGenre(array $validatedData): Genre
    {
        return Genre::create($validatedData);
    }

    /**
     * ジャンルを更新
     * @param Genre $genre
     * @param array $validatedData
     * @return bool
     */
    public function updateGenre(Genre $genre, array $validatedData): bool
    {
        return $genre->update($validatedData);
    }

    /**
     * ジャンルを削除
     * ※紐づくジャンルがあれば削除対象外にする
     * @param Genre $genre
     * @return bool
     */
    public function deleteGenre(Genre $genre): bool
    {   // Genreクラスが入ったオブジェクトを、モデルに定義したリレーションでbookモデルを見に行って、bookモデルに紐づいているgenreかを確認している、紐づいていればfalse
        if ($genre->books()->exists()) {
            return false;
        }

        $genre->delete();

        return true;
    }
}
