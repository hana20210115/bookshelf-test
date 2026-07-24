<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\Book;
use App\Http\Requests\Book\UpdateBookRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\Genre;
use App\Http\Requests\Book\StoreBookRequest;
use Illuminate\Support\Facades\Auth;


class BookController extends Controller
{   
    //用意したサービスを入れておくための変数、ここでしか使えない
    private $bookService;

    /**
     * コンストラクタ
     * @param BookService $bookService
     * 
     */
    //このコントローラーが呼ばれた時、自動でBookServiceを渡してくれる
    public function __construct(BookService $bookService)
    {   
        //このクラスのbookServiceプロパティにbookServiceクラスを代入する
        $this->bookService = $bookService;
    }

    /**
     * 書籍一覧を表示する
     *
     * @param Request $request
     * @return view
     */
    public function index(Request $request):view
    {
        $params = $request->only(['keyword','genre','sort']);

        $books = $this->bookService->getBookList($params);

        return view('books.index',compact('books','params'));
    }

    /**
     * 書籍詳細を表示する
     *
     */
    public function show(Book $book)
    {

    $book = $this->bookService->getBookDetails($book);

    return view('books.show',compact('book'));
    }
    
    /**
     * 書籍登録画面の表示
     * @return View
     */
    public function create():View
    {
        //ジャンルを選択できるようにジャンルを全て取得する
        $genres = Genre::all();

        return view('books.create',compact('genres'));
    }

    /**
     * 書籍の登録処理
     * @param StoreBookRequest $request
     * @return RedirectResponse
     */
    public function store(StoreBookRequest $request):RedirectResponse
    {   
        

        //サービスにバリデーション済みのデータを渡して保存処理をさせる
        $this->bookService->storeBook($request->validated(),Auth::id());

        //書籍登録画面へリダイレクト
        return redirect()->route('books.create')->with('success','新しい書籍を登録しました');


    }

    /**
     * 書籍の編集画面へ遷移する
     * @param Book $book
     */
    public function edit(Book $book):view
    {
        $this->authorize('update',$book);


        $date = $this->bookService->getEditDate($book);



        return view('books.edit',$date);//getEditDateの中で配列に変えてるので、compact関数は使わない
    }

    /**
     *
     * 書籍の更新処理
     * @param UpdateBookRequest
     * @param Book $book
     * @return RedirectResponse
     * 
     */
    public function update(UpdateBookRequest $request,Book $book):RedirectResponse
    {   //認可チェック
        $this->authorize('update',$book);

        //UpdateBookRequestのチェックを通過したデータのみ配列として取得
        $this->bookService->updateBook($book,$request->validated());

        //更新完了後、書籍の詳細画面へリダイレクト
        return redirect()->route('books.show',$book)->with('success','書籍の情報を更新しました');

    }

    /**
     * 書籍の削除処理
     * ※単純なロジックなのでサービスはクラスは使いません
     * @param Book $book
     * @return RedirectResponse
     */
    public function destroy(Book $book):RedirectResponse
    {
        //認可チェック
        $this->authorize('delete',$book);

        $book->delete();

        return redirect()->route('books.index')->with('success','書籍を削除しました');

        
    }


}
