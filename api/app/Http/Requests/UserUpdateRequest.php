<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|required|string|max:50',
            'middle_initial' => 'sometimes|nullable|string|size:1',
            'gender' => 'sometimes|required|in:male,female',
            'phone_no' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:active,inactive',
            'role' => 'sometimes|required|in:admin,client,superadmin',
            'username' => 'sometimes|required|string|max:50',
            'password' => 'sometimes|required|string|max:255',
        ];
    }
}
