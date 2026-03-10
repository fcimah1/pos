<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'method' => ['required', 'in:cash,card,check,digital_wallet'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_number' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'order_id.required' => 'يرجى تحديد رقم الطلب.',
            'order_id.integer' => 'رقم الطلب غير صالح.',
            'order_id.exists' => 'رقم الطلب غير موجود.',
            'method.required' => 'يرجى تحديد طريقة الدفع.',
            'method.in' => 'طريقة الدفع غير صالحة.',
            'amount.required' => 'يرجى تحديد المبلغ.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقمًا.',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من 0.01.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'البيانات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422));
    }
}
