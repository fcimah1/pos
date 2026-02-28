<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'phone' => ['required','string','max:30'],
            'email' => ['nullable','email'],
            'vehicle_type' => ['nullable','string'],
            'vehicle_number' => ['nullable','string'],
            'status' => ['sometimes','in:available,on_delivery,offline'],
            'is_active' => ['sometimes','boolean'],
        ];
    }
}
