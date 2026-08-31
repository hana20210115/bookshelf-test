<?php

namespace App\Http\Requests\ReadingPlan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReadingPlanRequest extends FormRequest
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
            'book_id' => ['required', 'exists:books,id'],
            'target_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => '対象書籍を選択して下さい',
            'target_date.after_or_equal' => '今日以降の日付を入力して下さい',
        ];
    }

    // 項目名の日本語化
    public function attributes(): array
    {
        return [
            'book_id' => '対象書籍',
            'target_date' => '期日',
        ];
    }
}
