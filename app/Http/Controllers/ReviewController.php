<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Models\Book;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    private $reviewService;

    /**
     * コンストラクタ
     */
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * レビューの新規投稿処理
     *
     * @param StoreReviewRequest
     */
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        $this->reviewService->storeReview($book, $request->validated(), Auth::id());

        return redirect()->route('books.show', $book)->with('success', 'レビューを投稿しました。');
    }

    /**
     * レビュー編集画面の表示
     */
    public function edit(Review $review): View
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * レビューの更新処理
     */
    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $this->reviewService->updateReview($review, $request->validated());

        return redirect()->route('reviews.edit', $review)->with('success', 'レビューを更新しました。');
    }

    /**
     * レビューの削除処理
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($review);

        return redirect()->route('books.show', $review->book_id)->with('success', 'レビューを削除しました。');
    }

    /**
     * レビューの「いいね」処理（追加・解除のトグル）
     */
    public function like(Review $review): RedirectResponse
    {
        $this->reviewService->toggleLike($review, Auth::id());

        return back();
    }
}
