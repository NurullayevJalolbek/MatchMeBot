<?php

namespace App\Http\Requests\Admin;

use App\Enums\Admin\AdminStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserUpdateRequest extends FormRequest
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
        $adminId = $this->route('admin')?->id ?? $this->route('admin');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($adminId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($adminId)],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['required', Rule::enum(AdminStatusEnum::class)],
        ];
    }

    /**
     * Custom validation messages in Uzbek.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Admin ism-familiyasini kiritish majburiy',
            'username.required' => 'Foydalanuvchi nomini (username) kiritish majburiy',
            'username.unique' => 'Ushbu username band, boshqa username kiriting',
            'email.required' => 'Email manzilini kiritish majburiy',
            'email.email' => 'Haqiqiy email manzilini kiriting',
            'email.unique' => 'Ushbu email allaqachon ro\'yxatdan o\'tgan',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
            'status.required' => 'Statusni tanlash majburiy',
        ];
    }
}
