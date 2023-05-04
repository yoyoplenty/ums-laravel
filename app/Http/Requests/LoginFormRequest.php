<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginFormRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'email' => 'bail|required|exists:users,email|email:filter',
            'password' => ['bail', 'required', 'max:127', Password::min(8)->letters()->mixedCase()->numbers()],
            'role_id' => ['bail', 'required', 'integer', 'exists:roles,id'],
        ];
    }
}
