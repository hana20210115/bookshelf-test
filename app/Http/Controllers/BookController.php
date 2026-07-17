<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\Book;

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
     * 書籍一覧を表示するメソッド
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
     * 書籍詳細を表示する(仮)
     *
     */
    public function show(Book $book){

    $book->load(['genres','reviews.user','reviews.likedByUsers']);

    return view('books.show',compact('book'));
    }
}
