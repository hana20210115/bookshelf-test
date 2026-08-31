<?php

namespace App\Http\Requests\Genre;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGenreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50', Rule::unique('genres', 'name')],
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
