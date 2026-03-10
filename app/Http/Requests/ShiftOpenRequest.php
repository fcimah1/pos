<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShiftOpenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opening_cash' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'opening_cash.required' => 'يرجى تحديد المبلغ المفتوح.',
            'opening_cash.numeric' => 'المبلغ المفتوح يجب أن يكون رقمًا.',
            'opening_cash.min' => 'المبلغ المفتوح يجب أن يكون أكبر من أو يساوي 0.',
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
