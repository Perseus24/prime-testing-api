<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTestCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $clean = fn ($list) => array_values(array_filter(array_map('trim', (array) $list), 'strlen'));

        $this->merge([
            'preconditions' => $clean($this->preconditions),
            'steps'         => $clean($this->steps),
            'expected'      => $clean($this->expected),
        ]);
}
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code'            => ['required', 'string', 'max:20', 'unique:test_cases,code'],
            'title'           => ['required', 'string', 'max:255'],
            'module'          => ['required', 'string', 'max:100'],
            'platform'        => ['required', 'in:web,mobile'],
            'priority'        => ['required', 'in:low,medium,high'],
            'preconditions'   => ['array'],
            'preconditions.*' => ['string', 'max:500'],
            'steps'           => ['required', 'array', 'min:1'],
            'steps.*'         => ['string', 'max:500'],
            'expected'        => ['required', 'array', 'min:1'],
            'expected.*'      => ['string', 'max:500'],
        ];
    }
}
