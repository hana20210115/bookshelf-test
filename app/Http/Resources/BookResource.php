<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * API用にデータをJSONに変換する
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'published_date' => $this->published_date,
            'image_url' => $this->image_url,

            'genres' => $this->whenLoaded('genres', function () {
                return $this->genres->map(function ($genre) {
                    return [
                        'id' => $genre->id,
                        'name' => $genre->name,
                    ];
                });
            }), // whenLoadedメソッドでBookモデルに定義したgenresメソッド（リレーション)を使って、手元のデータにジャンルが紐づいている時はこの後のループ分を処理して！と指示をしている。

            'rating_average' => round($this->reviews_avg_rating ?? 0, 1),
            // 星の数の平均点を小数点第二を四捨五入して第一までを表示する（１は小数点第一ということ）、nullなら０を返してということ

            'review_count' => $this->reviews_count ?? 0,
            // レビュー件数、なければ０を返してる

            'reviews' => $this->whenLoaded('reviews'),

        ];
    }
}
