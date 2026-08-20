<?php

namespace App\Http\Requests\Admin;

use App\Enums\Subscription\SubscriptionPeriodTypeEnum;
use App\Enums\Subscription\SubscriptionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionStoreRequest extends FormRequest
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
            'income_category_id' => [
                'nullable', 
                'integer', 
                Rule::exists('income_categories', 'id')->whereNotNull('parent_id')
            ],
            'title' => ['required', 'string', 'max:255'],
            'period_count' => ['required', 'integer', 'min:1', 'max:365'],
            'period_type' => ['required', Rule::enum(SubscriptionPeriodTypeEnum::class)],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:50'],
            'badge' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::enum(SubscriptionStatusEnum::class)],
            'order' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages in Uzbek.
     */
    public function messages(): array
    {
        return [
            'income_category_id.exists' => 'Ota kategoriyani tanlash mumkin emas! Iltimos, aniq ichki (bola) tushum kategoriyasini tanlang.',
            'title.required' => 'Obuna sarlavhasini kiritish majburiy',
            'period_count.required' => 'Davomiylik miqdorini kiritish majburiy',
            'period_count.integer' => 'Davomiylik faqat butun son bo\'lishi kerak',
            'period_type.required' => 'Davr turini (kun, hafta, oy, yil) tanlash majburiy',
            'price.required' => 'Sotuv narxini kiritish majburiy',
            'price.numeric' => 'Narx faqat raqam bo\'lishi kerak',
            'status.required' => 'Statusni tanlash majburiy',
        ];
    }
}
