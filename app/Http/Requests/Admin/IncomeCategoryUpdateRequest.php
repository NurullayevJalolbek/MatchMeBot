<?php

namespace App\Http\Requests\Admin;

use App\Enums\Finance\FinanceStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncomeCategoryUpdateRequest extends FormRequest
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
        $catId = $this->route('income_category')?->id ?? $this->route('income_category');

        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:income_categories,id', 'not_in:' . $catId],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::enum(FinanceStatusEnum::class)],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Custom validation messages in Uzbek.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tushum kategoriyasi nomini kiritish majburiy',
            'parent_id.not_in' => 'Kategoriya o\'z-o\'ziga ota kategoriya bo\'la olmaydi',
            'status.required' => 'Statusni tanlash majburiy',
        ];
    }
}
