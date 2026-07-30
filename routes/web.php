<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// ログインしているユーザーだけがアクセスできるページ
Route::middleware('auth')->group(function () {
    // 書籍登録画面の表示
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');

    // 書籍の登録処理
    Route::post('/books', [BookController::class, 'store'])->name('books.store');

    // お気に入り追加、解除ボタンを押す
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // レビュー投稿ボタンを押す
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // レビューへのいいね追加、解除
    Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');

    // 自分のレビューの編集画面へ遷移
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');

    // 自分のレビューの削除ボタンを押す
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // 自分のレビューの編集ボタンを押す
    Route::put('/review/{review}', [ReviewController::class, 'update'])->name('reviews.update');

    // 自分の書籍の編集画面を表示する
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');

    // 自分の書籍の編集を実行する
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');

    // 自分の書籍を削除する
    Route::delete('/books/{book}/delete', [BookController::class, 'destroy'])->name('books.destroy');

    // ランキング画面の表示
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    // お気に入りの一覧画面の表示
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // ジャンル管理画面の表示
    Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');

    // ジャンル登録画面へ遷移する
    Route::get('/genres/create', [GenreController::class, 'create'])->name('genres.create');
    // ジャンル登録処理
    Route::post('/genres', [GenreController::class, 'store'])->name('genres.store');

    // ジャンル詳細画面へ遷移する
    Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');

    // ジャンル編集画面へ遷移
    Route::get('/genres/{genre}/edit', [GenreController::class, 'edit'])->name('genres.edit');
    // ジャンル編集処理
    Route::put('/genres/{genre}', [GenreController::class, 'update'])->name('genres.update');

    // ジャンルの削除処理
    Route::delete('/genres/{genre}',[GenreController::class, 'destroy'])->name('genres.destroy');

});

// トップページ(書籍一覧)
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// 書籍詳細画面の表示
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// ログアウト後のリダイレクト先
Route::redirect('/', '/books');

// ランキング画面の表示（ゲストにも表示可）
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

