<?php
namespace App\Services;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Pagination\LengthAwarePaginator;


class BookService
{
    /**
     * 検索・ソート条件に応じた書籍一覧をページネーションで取得する
     * @param array $params リクエストパラメーター(keyword,genre,sort)
     * @return LengthAwarePaginator
     */
    public function getBookList(array $param = []):LengthAwarePaginator
    {   
        //ジャンル情報と、レビューの星の平均を一緒に持ってくる
        $query = Book::with('genres')->withAvg('reviews','rating');

        /*
        //検索機能（本タイトルか著者）
        // functionの入れ子になっているのは、AND（タイトル　OR 著者）の形をを作り、他の検索条件（ジャンル）などを汚さないようにしている
        $query->when($params['keyword'] ?? null,function ($q,$keyword{
            $q->where(function ($subQuery) use ($keyword){
                $subQuery->where('title','like',"%{$keyword)%")->orWhere('author','like',"%{$keyword}%");
            });
        });
        */

        //ジャンルの絞り込み機能
        //上記同様の理由で入れ子になっている
        //whereHasメソッドは自分が持っているモデルクラスじゃなく別テーブルを見に行ってくれるメソッド
        /*
        $query->when($params['genre'] ?? null, function ($q,$genre){
            $q->whereHas('genres',function($subQuery) use ($genre{
                $subQuery->where('name',$genre);
            });
        });
        */

        //並び替え機能
        /*
        $sort = $params['sort'] ?? 'latest'; //デフォルトはlatest(最新順)
        if($sort === 'latest') {
            $query->latest();
        }elseif ($sort === 'oldest'){
            $query->oldest();
        }elseif ($sort === 'title'){
            $query->orderBy('title','asc');
        }elseif ($sort === 'rating'){
            $query->orderByRaw('reviews_avg_rating IS NULL ASC')
            ->orderBy('reviews_avg_rating','desc');
        }
        */
        return $query->paginate(10)->withQueryString();
    }

    /**
     * 書籍詳細画面に必要なデータをロードして返す
     * 
     * @param Book $book
     * @return Book
     */
    public function getBookDetails(Book $book):Book
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);

        $book->loadCount('favorites');

        return $book;
    }

    /**
     * 編集画面に必要なデータを取得する
     */
    public function getEditDate(Book $book):array
    {
        return [
            'book' => $book,
            'genres' => Genre::all(),
            'bookGenreIds' => $book->genres->pluck('id')->toArray(),
        ];
    }

    /**
     * 書籍の更新処理
     */
    public function updateBook(Book $book,array $data):Book
    {   //booksテーブルを更新
        $book->update($data);

        //書籍とジャンルの中間テーブルのbook_genreテーブルを更新
        if (isset($data['genres'])){
            $book->genres()->sync($data['genres']);
        }

        return $book;

    }
}