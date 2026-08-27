<?php

namespace App\Http\Controllers;

use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use App\Services\BookService;
use App\Http\Requests\IsbnSearchRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    // 用意したサービスを入れておくための変数、ここでしか使えない
    private $bookService;

    /**
     * コンストラクタ
     */
    // このコントローラーが呼ばれた時、自動でBookServiceを渡してくれる
    public function __construct(BookService $bookService)
    {
        // このクラスのbookServiceプロパティにbookServiceクラスを代入する
        $this->bookService = $bookService;
    }

    /**
     * 書籍一覧を表示する
     */
    public function index(Request $request): View
    {
        $params = $request->only(['keyword', 'genre', 'sort']);

        //画面のドロップダウン用にすべてのジャンルを取得
        $genres = Genre::all();

        $books = $this->bookService->getBookList($params);

        return view('books.index', compact('books', 'params','genres'));
    }

    /**
     * 書籍詳細を表示する
     */
    public function show(Book $book)
    {

        $book = $this->bookService->getBookDetails($book);

        return view('books.show', compact('book'));
    }

    /**
     * 書籍登録画面の表示
     */
    public function create(): View
    {
        // ジャンルを選択できるようにジャンルを全て取得する
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 書籍の登録処理
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {

        // サービスにバリデーション済みのデータを渡して保存処理をさせる
        $this->bookService->storeBook($request->validated(), Auth::id());

        // 書籍登録画面へリダイレクト
        return redirect()->route('books.create')->with('success', '新しい書籍を登録しました');

    }

    /**
     * 書籍の編集画面へ遷移する
     */
    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        $date = $this->bookService->getEditDate($book);

        return view('books.edit', $date); // getEditDateの中で配列に変えてるので、compact関数は使わない
    }

    /**
     * 書籍の更新処理
     *
     * @param UpdateBookRequest
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {   // 認可チェック
        $this->authorize('update', $book);

        // UpdateBookRequestのチェックを通過したデータのみ配列として取得
        $this->bookService->updateBook($book, $request->validated());

        // 更新完了後、書籍の詳細画面へリダイレクト
        return redirect()->route('books.show', $book)->with('success', '書籍の情報を更新しました');

    }

    /**
     * 書籍の削除処理
     */
    public function destroy(Book $book): RedirectResponse
    {
        // 認可チェック
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました');

    }

    /**
     * ISBN検索（非同期処理通信API）
     *
     * @param IsbnSearchRequest $request
     * @return JsonResponse
     */
    public function searchByIsbn(IsbnSearchRequest $request): JsonResponse
    {
        $isbn = $request->validated('isbn');

        $result = $this->bookService->getBookInfoByIsbn($isbn);

        if (!$result['is_success']) {
            return response()->json(
                ['error' => $result['message']],
                $result['status']
            );
        }

        return response()->json($result['data']);
    }
}
