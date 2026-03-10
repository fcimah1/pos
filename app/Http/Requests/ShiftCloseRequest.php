<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShiftCloseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'closing_cash' => ['required', 'numeric', 'min:0'],
        ];
    }


    public function messages()
    {
        return [
            'closing_cash.required' => 'يرجى تحديد المبلغ المغلق.',
            'closing_cash.numeric' => 'المبلغ المغلق يجب أن يكون رقمًا.',
            'closing_cash.min' => 'المبلغ المغلق يجب أن يكون أكبر من أو يساوي 0.',
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
