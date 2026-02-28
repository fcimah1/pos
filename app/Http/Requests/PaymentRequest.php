<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required','integer','exists:orders,id'],
            'method' => ['required','in:cash,card,check,digital_wallet'],
            'amount' => ['required','numeric','min:0.01'],
            'reference_number' => ['nullable','string'],
            'notes' => ['nullable','string'],
        ];
    }
}
