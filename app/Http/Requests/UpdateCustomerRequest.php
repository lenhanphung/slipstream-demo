<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer');

        return [
            'name' => ['required', 'string', 'max:255'],
            'reference' => ['required', 'string', 'unique:customers,reference,' . $customerId, 'max:50'],
            'customer_category_id' => ['required', 'exists:customer_categories,id'],
            'start_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
