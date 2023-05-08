<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest {
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
        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                    return [];
                }
            case 'POST': {
                    return [
                        'firstname' => 'bail|required|alpha|max:20|min:3',
                        'lastname' => 'required|alpha|max:20|min:3',
                        'middlename' => "sometimes|string|min:4|max:30",
                        'email' => 'required|string|email:rfc,dns|max:150|unique:users,email',
                        'password' => "required|string|min:5|max:30",
                        'phone_number' => 'sometimes|string|min:11|max:15',
                        'role_id' => 'required|integer|exists:App\Models\Role,id',
                    ];
                }
            case 'PUT':
            case 'PATCH': {
                    return [
                        'uuid' => 'required|exists:users,uuid',
                        'firstname' => 'sometimes|alpha|max:20|min:3',
                        'lastname' => 'sometimes|alpha|max:20|min:3',
                        'middlename' => "sometimes|string|min:3|max:30",
                        'phone_number' => 'sometimes|string|min:11|max:15',
                    ];
                }
            default:
                break;
        }
    }
}
