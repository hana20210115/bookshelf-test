<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class BookService
{
    /**
     * 検索・ソート条件に応じた書籍一覧をページネーションで取得する
     *
     * @param  array  $params  リクエストパラメーター(keyword,genre,sort)
     * @return LengthAwarePaginator
     */
    public function getBookList(array $params = []): LengthAwarePaginator
    {
        // ジャンル情報と、レビューの星の平均を一緒に持ってくる
        $query = Book::with('genres')->withAvg('reviews', 'rating');

        
        //検索機能（本タイトルか著者）
        // functionの入れ子になっているのは、AND（タイトル　OR 著者）の形をを作り、他の検索条件（ジャンル）などを汚さないようにしている

        $query->when($params['keyword'] ?? null,function ($q,$keyword){
            $q->where(function ($subQuery) use ($keyword){
                $subQuery->where('title','like',"%{$keyword}%")->orWhere('author','like',"%{$keyword}%");
            });
        });
        

        // ジャンルの絞り込み機能
        // 上記同様の理由で入れ子になっている
        // whereHasメソッドは自分が持っているモデルクラスじゃなく別テーブルを見に行ってくれるメソッド
        
        $query->when($params['genre'] ?? null, function ($q,$genre){
            $q->whereHas('genres',function($subQuery) use ($genre){
                $subQuery->where('genre_id',$genre);
            });
        });


        // 並び替え機能

        $sort = $params['sort'] ?? 'latest'; //デフォルトはlatest(最新順)
        if($sort === 'newest') {
            $query->latest();
        }elseif ($sort === 'oldest'){
            $query->oldest();
        }elseif ($sort === 'title'){
            $query->orderBy('title','asc');
        }elseif ($sort === 'rating'){
            $query->orderByRaw('reviews_avg_rating IS NULL ASC')
            ->orderBy('reviews_avg_rating','desc');
        }

        return $query->paginate(10)->withQueryString();
    }

    /**
     * 書籍詳細画面に必要なデータをロードして返す
     * @param Book $book
     * @return Book
     */
    public function getBookDetails(Book $book): Book
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);


        return $book;
    }

    /**
     * 書籍の新規登録
     *
     * @param array $validateDate
     * @param int $userId
     * @return Book
     *
     */
    public function storeBook(array $validatedData, int $userId): Book
    {
        // バリデーションデータからジャンルのIDを取る
        $genreId = $validatedData['genres'];

        // 書籍の保存には不要なジャンルを切り離す
        unset($validatedData['genres']);

        // このままだと、$validatedDataの中にはuser_idがないので追加する
        $validatedData['user_id'] = $userId;

        // データベスに保存
        $book = Book::create($validatedData);

        // 中間テーブルに紐付け
        $book->genres()->sync($genreId);

        return $book;

    }

    /**
     * 編集画面に必要なデータを取得する
     * @param Book $book
     * @return array
     */
    public function getEditDate(Book $book): array
    {
        return [
            'book' => $book,
            'genres' => Genre::all(),
            'bookGenreIds' => $book->genres->pluck('id')->toArray(),
        ];
    }

    /**
     * 書籍の更新処理
     * @param Book $book
     * @param array $data
     * @return Book
     */
    public function updateBook(Book $book, array $data): Book
    {   // booksテーブルを更新
        $book->update($data);

        // 書籍とジャンルの中間テーブルのbook_genreテーブルを更新
        if (isset($data['genres'])) {
            $book->genres()->sync($data['genres']);
        }

        return $book;

    }

    /**
     * ISBNから書籍情報を取得する(Google Books API)
     * 
     * @param string $isbn
     * @return array|null
     */
    public function getBookInfoByIsbn(string $isbn): ?array
    {
        $response = Http::get("https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}");

        //通信が成功し、1件以上のデータが見つかったら、最初の1件のデータを返す
        if ($response->successful() && $response->json('totalItems') > 0){
            $bookData = $response->json('items')[0]['volumeInfo'];

            return [
                'title' => $bookData['title'] ?? '',
                'author' => isset($bookData['authors']) ? implode(', ', $bookData['authors']) : '',
                'published_date' => $bookData['publishedDate'] ?? '',
                'description' => $bookData['description'] ?? '',
                'image_url' => $bookData['imageLinks']['thumbnail'] ?? '',
            ];
        }

        return null;
    }
}
