<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookApiRequest extends FormRequest
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
            'title' => ['required','string','max:255'],
            'author' => ['required','string','max:255'],
            'isbn' => ['nullable','string','size:13','unique:books,isbn'],
            'published_date' => ['nullable','date'],
            'genres' => ['required','array','min:1'],
            'genres.*' => ['exists:genres,id'],
            'image_url' => ['nullable','url'],
            'user_id' => ['required','integer','exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください',
            'title.max' => 'タイトルは255文字以内で入力してください',
            'author.required' => '著者を入力してください',
            'author.max' => '著者は255文字以内で入力してください',
            'isbn.size' => 'ISBNは13文字で入力してください',
            'isbn.unique' => 'このISBNはすでに登録されています',
            'published_date.date' => '有効な日付を入力してください',
            'image_url.url' => '正しいURL形式で入力してください',
            'genres.required' => 'ジャンルを選択してください',
            'genres.*.exists' => '選択されたジャンルは存在しません',
            'user_id.required' => '登録者IDを入力してください',
            'user_id.exists' => '指定された登録者IDは存在しません',
        ];
    }
}