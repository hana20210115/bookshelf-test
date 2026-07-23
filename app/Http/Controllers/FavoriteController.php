<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use App\Services\FavoriteService;

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
    public function toggle(Book $book):RedirectResponse
    {
        $user = auth()->user();

        $this->favoriteService->toggleFavorite($book,$user);

        return back();
    }
}
