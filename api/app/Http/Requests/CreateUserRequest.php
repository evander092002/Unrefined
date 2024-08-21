<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'middle_initial' => 'nullable|string|max:1',
            'gender' => 'required|in:male,female',
            'phone_no' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'role' => 'required|in:admin,client',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|max:255',
        ];
    }
}
