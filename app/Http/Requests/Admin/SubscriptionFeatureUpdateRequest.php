<?php

namespace App\Http\Requests\Admin;

use App\Enums\Subscription\SubscriptionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionFeatureUpdateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'status' => ['required', Rule::enum(SubscriptionStatusEnum::class)],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Custom validation messages in Uzbek.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Afzallik sarlavhasini kiritish majburiy',
            'icon_file.mimes' => 'Ikonka faqat PNG, JPG, JPEG, SVG yoki WEBP formatida bo\'lishi kerak',
            'icon_file.max' => 'Ikonka hajmi 2MB dan oshmasligi kerak',
            'status.required' => 'Statusni tanlash majburiy',
        ];
    }
}
