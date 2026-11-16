<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResultRequest extends FormRequest
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
            'result' => ['required', 'in:passed,failed,blocked'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'severity' => ['nullable', 'required_if:result,failed', 'in:minor,major,critical'],
            'frequency' => ['nullable', 'required_if:result,failed', 'in:every,sometimes,once'],
            'device' => ['nullable', 'required_if:result,failed', 'string', 'max:255'],
        ];
    }
}
