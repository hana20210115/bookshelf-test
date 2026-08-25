<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReadingPlanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

// ログインしているユーザーだけがアクセスできるページ
Route::middleware('auth')->group(function () {
    //isbn検索APIのルート
    Route::get('/books/isbn/{isbn}', [BookController::class, 'searchByIsbn'])->name('books.searchByIsbn');

    // 書籍登録画面の表示
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');

    // 書籍登録時のISBN検索用API
    Route::get('/books/search-isbn', [BookController::class, 'searchIsbn'])->name('books.search-isbn');

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

    // お気に入りの一覧画面の表示
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // ジャンル（一覧、作成、保存、編集、更新、削除）
    Route::resource('genres', GenreController::class);

    // マイ読書レポート
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // 読書計画（一覧、作成、保存、編集、更新、削除）
    Route::resource('reading-plans', ReadingPlanController::class)->except(['show']);

    // 読書計画の『読了』アクション
    Route::post('/reading-plans/{reading_plan}/complete', [ReadingPlanController::class, 'complete'])->name('reading-plans.complete');

    // 通知一覧表示
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // 通知既読化
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});


// トップページ(書籍一覧)
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// 書籍詳細画面の表示
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// ログアウト後のリダイレクト先
Route::redirect('/', '/books');

// ランキング画面の表示（ゲストにも表示可）
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');