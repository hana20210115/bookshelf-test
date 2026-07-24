<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
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

//トップページ(書籍一覧)
Route::get('/books',[BookController::class,'index'])->name('books.index');

//ログインしているユーザーだけがアクセスできるページ
Route::middleware('auth')->group(function()
{
    //書籍登録画面の表示
    Route::get('/books/create',[BookController::class,'create'])->name('books.create');

    //書籍の登録処理
    Route::post('/books',[BookController::class,'store'])->name('books.store');

    //お気に入り追加、解除ボタンを押す
    Route::post('/books/{book}/favorites',[FavoriteController::class,'toggle'])->name('favorites.toggle');

    //レビュー投稿ボタンを押す
    Route::post('/books/{book}/reviews',[ReviewController::class,'store'])->name('reviews.store');

    //レビューへのいいね追加、解除
    Route::post('/reviews/{review}/like',[ReviewController::class,'like'])->name('reviews.like');

    //自分のレビューの編集画面へ遷移
    Route::get('/reviews/{review}/edit',[ReviewController::class,'edit'])->name('reviews.edit');

    //自分のレビューの削除ボタンを押す
    Route::delete('/reviews/{review}',[ReviewController::class,'destroy'])->name('reviews.destroy');

    //自分のレビューの編集ボタンを押す
    Route::put('/review/{review}',[ReviewController::class,'update'])->name('reviews.update');

    //自分の書籍の編集画面を表示する
    Route::get('/books/{book}/edit',[BookController::class,'edit'])->name('books.edit');

    //自分の書籍の編集を実行する
    Route::put('/books/{book}',[BookController::class,'update'])->name('books.update');

    //自分の書籍を削除する
    Route::delete('/books/{book}/delete',[BookController::class,'destroy'])->name('books.destroy');



});

//書籍詳細画面の表示
Route::get('/books/{book}',[BookController::class,'show'])->name('books.show');

