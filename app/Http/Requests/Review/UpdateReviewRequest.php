<?php

namespace App\Http\Requests\Review;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'], // 星の数の下限上限、基本的には1〜５の間でしか入力で着ないが悪意のあるユーザー対策で一応定めている
            'comment' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価を入力してください',
            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは1000文字以下で入力してください',
        ];

    }
}
