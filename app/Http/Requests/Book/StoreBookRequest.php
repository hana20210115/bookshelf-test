<?php

namespace App\Http\Requests\Book;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'size:13', 'unique:books,isbn'],
            'description' => ['nullable', 'string', 'max:1000'],
            'published_date' => ['nullable', 'date'],
            'genres' => ['required', 'array'],
            'genres.*' => ['exists:genres,id'], // 画面で選択されたジャンルが配列で送られてくるから中身を取り出して、genresテーブルのidカラムに本当にあるか確認してる
            'image_url' => ['nullable', 'url'],

        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください',
            'title.max' => 'タイトルは255文字以内で入力してください',
            'author.required' => '著者名を入力してください',
            'author.max' => '著者名は255文字以下で入力してください',
            'isbn.size' => 'ISBNは13桁で入力してください',
            'isbn.unique' => '有効なISBNを入力してください',
            'description.max' => '説明文は1000文字以下で入力してください',
            'published_date.date' => '有効な日付を入力してください',
            'genres.required' => 'ジャンルを選択してください',
            'genres.*.exists' => '選択されたジャンルが正しくありません',
            'image_url.url' => 'URL形式で入力してください',
        ];

    }
}
