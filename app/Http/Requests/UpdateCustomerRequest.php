<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|regex:/^[0-9]+$/|unique:customers,phone,' . $this->route('customer'),
            'phone2' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'special_mark' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'الاسم يجب أن لا يتجاوز 255 حرفاً',
            'phone.max' => 'رقم الهاتف يجب أن لا يتجاوز 20 رقم',
            'phone.regex' => 'رقم الهاتف يجب أن يحتوي على أرقام فقط',
            'phone.unique' => 'رقم الهاتف مسجل لعميل آخر',
            'phone2.regex' => 'رقم الهاتف 2 يجب أن يحتوي على أرقام فقط',
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
