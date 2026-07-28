<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchBookApiRequest extends FormRequest
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
            'keyword' => ['nullable', 'string', 'max:100'],
            'genre_id' => ['nullable', 'integer', 'exists:genres,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.max' => '検索ワードは100文字以内で入力してください',
            'genre_id.exists' => '指定されたジャンルは存在しません',
            'page.integer' => 'ページ番号は整数で指定してください',
            'page.min' => 'ページ番号は1以上を指定してください',
            'per_page.integer' => '1ページあたりの件数は整数で指定してください',
            'per_page.min' => '1ページあたりの件数は1以上を指定してください',
            'per_page.max' => '1ページあたりの件数は最大100件までです',
        ];
    }
}
