<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    public function messages()
    {
        return [
            'name.required' => 'يرجى إدخال اسم التوصيل.',
            'phone.required' => 'يرجى إدخال رقم الهاتف.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'vehicle_type.string' => 'نوع المركبة غير صالح.',
            'vehicle_number.string' => 'رقم المركبة غير صالح.',
            'status.in' => 'حالة التوصيل غير صالحة.',
            'is_active.boolean' => 'يجب أن تكون حالة التوصيل منطقية.',
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
