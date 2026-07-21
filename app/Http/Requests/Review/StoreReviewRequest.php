<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'rating' => ['required','integer','min:1','max:5'],//星の数の下限上限
            'comment' => ['nullable','string','max'],
        ];
    }

    public function messages():array
    {
        return[
            'rating.required' => '評価を入力してください',
            'comment.max:1000' => 'コメントは1000文字以下で入力してください',
        ];
        
    }
}
