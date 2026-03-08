<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone|regex:/^[0-9]+$/',
            'phone2' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'special_mark' => 'nullable|string|max:500',
            'branch_id' => 'sometimes|exists:branches,id',
            'is_active' => 'sometimes|boolean',
            'addresses' => 'nullable|array',
            'addresses.*.address_line_1' => 'required|string|max:255',
            'addresses.*.address_line_2' => 'nullable|string|max:255',
            'addresses.*.floor_number' => 'nullable|string|max:50',
            'addresses.*.apartment_number' => 'nullable|string|max:50',
            'addresses.*.delivery_charge' => 'nullable|numeric|min:0',
            'addresses.*.type' => 'required|in:home,work,other',
            'addresses.*.is_default' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف موجود بالفعل قبل ذلك',
            'phone.regex' => 'رقم الهاتف (الموبايل) يجب أن يحتوي على أرقام فقط',
            'phone.max' => 'رقم الهاتف (الموبايل) يجب أن لا يتجاوز 20 رقم',
            'phone2.regex' => 'رقم الموبايل 2 يجب أن يحتوي على أرقام فقط',
            'phone2.max' => 'رقم الموبايل 2 يجب أن لا يتجاوز 20 رقم',
            'addresses.*.address_line_1.required' => 'يجب إدخال العنوان لكل حقل مضاف',
            'addresses.*.address_line_1.max' => 'العنوان طويل جداً (أقصى 255 حرف)',
            'addresses.*.type.required' => 'يجب اختيار نوع العنوان',
            'addresses.*.type.in' => 'نوع العنوان غير صالح',
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
