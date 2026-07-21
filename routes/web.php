<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
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

//書籍詳細画面の表示
Route::get('/books/{book}',[BookController::class,'show'])->name('books.show');

//ログインしているユーザーだけがアクセスできるページ
Route::middleware('auth')->group(function()
{
    //書籍登録画面の表示
    Route::get('/books/create',[BookController::class,'create'])->name('books.create');

});

