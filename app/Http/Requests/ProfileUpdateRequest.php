<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    /**
     * Локализованные сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно для заполнения.',
            'name.max' => 'Имя не должно превышать 255 символов.',
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Введите корректный email адрес.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'email.max' => 'Email не должен превышать 255 символов.',
        ];
    }
}
