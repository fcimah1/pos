<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required','integer','exists:customers,id'],
            'type' => ['required','in:home,work,other'],
            'address_line_1' => ['required','string','max:255'],
            'address_line_2' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:100'],
            'state' => ['nullable','string','max:100'],
            'postal_code' => ['nullable','string','max:20'],
            'country' => ['nullable','string','max:100'],
            'latitude' => ['nullable','numeric'],
            'longitude' => ['nullable','numeric'],
            'notes' => ['nullable','string'],
            'is_default' => ['sometimes','boolean'],
        ];
    }
}
