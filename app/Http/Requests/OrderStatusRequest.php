<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // مؤقتاً للعمل بدون مصادقة
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:open,suspended,preparing,delivering,paid,completed,cancelled'],
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'يرجى تحديد حالة الطلب.',
            'status.in' => 'حالة الطلب غير صالحة.',
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
