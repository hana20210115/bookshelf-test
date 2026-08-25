<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookApiRequest;
use App\Http\Requests\Api\UpdateBookApiRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookApiController extends Controller
{
    private BookApiService $bookApiService;

    public function __construct(BookApiService $bookApiService)
    {
        $this->bookApiService = $bookApiService;
    }

    /**
     * 一覧取得
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $books = $this->bookApiService->getList($request);

        return BookResource::collection($books);
    }

    /**
     * 詳細取得
     */
    public function show(Book $book): BookResource
    {
        $book->load(['genres', 'reviews']);

        $book->loadCount('reviews');
        $book->loadAvg('reviews', 'rating');

        // 単一データの場合は　new を使う
        return new BookResource($book);
    }

    /**
     * 新規登録
     *
     * @param  StoreBookRequest  $request
     */
    public function store(StoreBookApiRequest $request): JsonResponse
    {
        $data =$request->validated();

        $data['user_id'] = $request->user()->id;

        $book = $this->bookApiService->create($data);

        $book->load('genres');

        return response()->json(new BookResource($book),201);
    }

    /**
     * 更新処理
     *
     * @param  UpdateBookRequest  $request
     */
    public function update(UpdateBookApiRequest $request, Book $book): JsonResponse
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'この操作は許可されていません。'], 403);
        }

        $book = $this->bookApiService->update($book, $request->validated());

        $book->load('genres');

        return response()->json(new BookResource($book));
    }

    /**
     * 削除処理
     */
    public function destroy(Request $request, Book $book): JsonResponse
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'この操作は許可されていません。'], 403);
        }
        $book->delete();

        return response()->json(null, 204);
    }
}
