<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTesterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role'  => ['required', 'in:admin,tester'],
            'code'  => ['required', 'string', 'max:20', 'unique:testers,code'],
            'name'  => ['required', 'string', 'max:255'],
            'lgu'   => ['required', 'string', 'max:255'],
            'position'  => ['nullable', 'string', 'max:255'],
        ];
    }
}
