<?php

namespace App\Http\Controllers;

use App\Http\Requests\Genre\StoreGenreRequest;
use App\Http\Requests\Genre\UpdateGenreRequest;
use App\Models\Genre;
use App\Services\GenreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GenreController extends Controller
{
    //
    private GenreService $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    /**
     * ジャンル一覧画面表示
     */
    public function index(): View
    {
        $genres = $this->genreService->getGenresWithBookCount();

        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンル詳細画面表示
     */
    public function show(Genre $genre): View
    {
        $books = $this->genreService->getPaginatedBooksGenre($genre);

        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * ジャンル作成画面表示
     */
    public function create(): View
    {
        return view('genres.create');
    }

    /**
     * ジャンル編集画面表示
     */
    public function edit(Genre $genre): View
    {
        return view('genres.edit', compact('genre'));
    }

    /**
     * ジャンル登録処理
     */
    public function store(StoreGenreRequest $request): RedirectResponse
    {
        $this->genreService->storeGenre($request->validated());

        return redirect()->route('genre.create')->with('success', 'ジャンルを登録しました');
    }

    /**
     * ジャンル更新処理
     */
    public function update(UpdateGenreRequest $request, Genre $genre): RedirectResponse
    {
        $this->genreService->UpdateGenre($genre, $request->validated());

        return redirect()->route('genres.index')->with('success', 'ジャンルを更新しました');
    }

    /**
     * ジャンル削除処理
     */
    public function destroy(Genre $genre): RedirectResponse
    {
        $isDeleted = $this->genreService->deleteGenre($genre);

        if (! $isDeleted) {
            return back()->with(['error' => '書籍が紐づいているため、このジャンルは削除できません']);
        }

        return redirect()->route('genres.index')->with('success', 'ジャンルを削除しました');

    }
}
