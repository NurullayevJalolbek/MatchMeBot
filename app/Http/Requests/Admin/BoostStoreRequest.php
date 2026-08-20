<?php

namespace App\Http\Requests\Admin;

use App\Enums\Boost\BoostStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BoostStoreRequest extends FormRequest
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
            'income_category_id' => ['nullable', 'integer', 'exists:income_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'hours' => ['required', 'integer', 'min:1', 'max:720'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'badge' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::enum(BoostStatusEnum::class)],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Custom validation messages in Uzbek.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Boost sarlavhasini kiritish majburiy',
            'hours.required' => 'Boost davomiylik soatini kiritish majburiy',
            'hours.integer' => 'Soat faqat butun son bo\'lishi kerak',
            'hours.min' => 'Soat kamida 1 bo\'lishi kerak',
            'price.required' => 'Sotuv narxini kiritish majburiy',
            'price.numeric' => 'Narx faqat raqam bo\'lishi kerak',
            'status.required' => 'Statusni tanlash majburiy',
        ];
    }
}
