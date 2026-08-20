<?php

namespace App\Http\Requests\Admin;

use App\Enums\Finance\ExpenseStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseUpdateRequest extends FormRequest
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
            'expense_category_id' => [
                'required',
                'integer',
                Rule::exists('expense_categories', 'id')->whereNotNull('parent_id'),
            ],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::enum(\App\Enums\Finance\PaymentMethodEnum::class)],
            'spent_at' => ['required', 'date'],
            'status' => ['required', Rule::enum(ExpenseStatusEnum::class)],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Custom validation messages in Uzbek.
     */
    public function messages(): array
    {
        return [
            'expense_category_id.required' => 'Xarajat kategoriyasini tanlash majburiy',
            'expense_category_id.exists' => 'Ota kategoriyani tanlash mumkin emas! Iltimos, aniq ichki (bola) xarajat kategoriyasini tanlang.',
            'title.required' => 'Xarajat sarlavhasi (nomi)ni kiritish majburiy',
            'amount.required' => 'Xarajat summasini kiritish majburiy',
            'amount.numeric' => 'Summa faqat raqam bo\'lishi kerak',
            'amount.min' => 'Summa 0 dan kam bo\'lishi mumkin emas',
            'spent_at.required' => 'Xarajat qilingan sana va vaqtni kiritish majburiy',
            'status.required' => 'Statusni tanlash majburiy',
        ];
    }
}
