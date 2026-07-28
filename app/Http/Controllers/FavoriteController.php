<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\FavoriteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\view\View;

class FavoriteController extends Controller
{
    private FavoriteService $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * お気に入りの追加/解除アクション
     */
    public function toggle(Book $book): RedirectResponse
    {
        $user = auth()->user();

        $this->favoriteService->toggleFavorite($book, $user);

        return back();
    }

    /**
     * お気にいに追加した書籍の一覧画面の表示
     */
    public function index(): View
    {
        $user = auth()->user();

        // サービスに$userがお気に入りに登録している書籍を取って来させる
        $books = $this->favoriteService->getFavoriteBooks($user);

        return view('favorites.index', compact('books'));

    }
}
