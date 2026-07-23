<?php

namespace App\Http\Requests\Genre;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
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
        $genreId = $this->route('genre');

        return [
            'name' => ['required','string','max:50',Rule::unique('genres','name')->ignore($genreId)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ジャンルを入力してください',
            'name.max' => 'ジャンル名は50文字以内で入力してください',
            'name.unique' => '有効なジャンルを入力してください',
        ];
    }
}
