<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required','numeric','min:0'],
            'category' => ['nullable','string','max:191'],
            'notes' => ['nullable','string'],
            'shift_id' => ['nullable','exists:shifts,id'],
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'يجب إدخال المبلغ',
            'amount.numeric' => 'يجب إدخال رقمًا',
            'amount.min' => 'يجب إدخال مبلغًا أكبر من أو يساوي صفر',
            'category.max' => 'يجب إدخال فئة مصروفًا أقصى 191 حرفًا',
            'notes.max' => 'يجب إدخال ملاحظات مصروفًا أقصى 191 حرفًا',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'البيانات غير صحيحة',
            'errors' => $validator->errors()
        ], 422));
    }

}
