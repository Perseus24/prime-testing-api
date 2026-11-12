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
            'result'     => ['required', 'in:pass,fail,blocked'],
            'notes'      => ['required_if:result,fail,blocked', 'nullable', 'string', 'max:2000'],
            'severity'   => ['required_if:result,fail', 'nullable', 'in:minor,major,critical'],
            'frequency'  => ['required_if:result,fail', 'nullable', 'in:every,sometimes,once'],
            'device'     => ['nullable', 'string', 'max:150'],
            'screenshot' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
