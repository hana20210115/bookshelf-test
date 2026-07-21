<?php

namespace App\Http\Requests\Book;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        // このクラスのURLのパスパラメーター{book}に入ったもの（例：id=1)を変数bookIdに入れている
        $bookId = $this->route('book');

        return [
            'title' => ['required','string','max:255'],
            'author' => ['required','string','max:255'],
            'isbn' => ['nullable','string','size:13',Rule::unique('books','isbn')->ignore($bookId)],//unique設定にしたら自分の登録の時に入力した番号と被って弾かれれしまうので、自分のisbn番号は除外してる
            'published_date' => ['nullable','date'],
            'genres' => ['required','array'],
            'genres.*' => ['exists:genres,id'],
            'image_url' => ['nullable','url'],

        ];
    }

    public function massages():array
    {
        return[
            'title.required' => 'タイトルを入力して下さい',
            'title.max' => 'タイトルは255文字以内で入力して下さい',
            'author,required' => '著者名を入力して下さい',
            'author.max' => '著者名は255文字で入力して下さい',
            'isbn.size'=> 'ISBNは13桁で入力して下さい',
            'isbn.unique' => 'このISBNはすでに登録されています'
            'published_date.date' => '有効な日付を入力して下さい',
            'genres.required' => 'ジャンルを選択して下さい',
            'genres.*.exists' => '選択されたジャンルが正しくありません',
            'image_url.url' => 'URL形式で入力して下さい',
        ];

    }
}
