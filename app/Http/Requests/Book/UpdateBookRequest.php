<?php

namespace App\Http\Requests\Book;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
        // このクラスのURLのパスパラメーター{book}に入ったもの（例：id=1)を変数bookIdに入れている
        $bookId = $this->route('book');

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'size:13', Rule::unique('books', 'isbn')->ignore($bookId)], // unique設定にしたら自分の登録の時に入力した番号と被って弾かれれしまうので、自分のisbn番号は除外してる
            'description' => ['nullable', 'max:1000'],
            'published_date' => ['nullable', 'date'],
            'genres' => ['required', 'array'],
            'genres.*' => ['exists:genres,id'],
            'image_url' => ['nullable', 'url'],

        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください',
            'title.max' => 'タイトルは255文字以内で入力してください',
            'author.required' => '著者名を入力してください',
            'author.max' => '著者名は255文字で入力して下さい',
            'isbn.size' => 'ISBNは13桁で入力してください',
            'isbn.unique' => 'このISBNはすでに登録されています',
            'description.max' => '説明文は1000文字以下で入力してください',
            'published_date.date' => '有効な日付を入力してください',
            'genres.required' => 'ジャンルを選択してください',
            'genres.*.exists' => '選択されたジャンルが正しくありません',
            'image_url.url' => 'URL形式で入力してください',
        ];

    }
}
