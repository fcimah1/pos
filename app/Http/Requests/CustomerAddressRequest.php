<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('patch') || $this->isMethod('put');
        return [
            'customer_id' => [$isUpdate ? 'sometimes' : 'required','integer','exists:customers,id'],
            'type' => [$isUpdate ? 'sometimes' : 'required','in:home,work,other'],
            'address_line_1' => [$isUpdate ? 'sometimes' : 'required','string','max:255'],
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
    
    public function messages()
    {
        return [
            'customer_id.required' => 'يجب إدخال معرف العميل',
            'type.required' => 'يجب إدخال نوع العنوان',
            'address_line_1.required' => 'يجب إدخال السطر الأول من العنوان',
            'is_default.boolean' => 'يجب إدخال قيمة منطقية للعنوان الافتراضي',
            'type.in' => 'يجب إدخال نوع عنوانًا صالحًا',
            'address_line_1.max' => 'يجب إدخال السطر الأول من العنوان أقصى 255 حرفًا',
            'address_line_2.max' => 'يجب إدخال السطر الثاني من العنوان أقصى 255 حرفًا',
            'city.max' => 'يجب إدخال المدينة أقصى 100 حرفًا',
            'state.max' => 'يجب إدخال الحالة أقصى 100 حرفًا',
            'postal_code.max' => 'يجب إدخال الرمز البريدي أقصى 20 حرفًا',
            'country.max' => 'يجب إدخال البلد أقصى 100 حرفًا',
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
