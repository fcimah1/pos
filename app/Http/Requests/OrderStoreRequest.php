<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['nullable','integer','exists:branches,id'],
            'user_id' => ['nullable','integer','exists:users,id'],
            'type' => ['required', Rule::in(['table','delivery','takeaway'])],
            'table_number' => ['nullable','string'],
            // طلب التوصيل يتطلب عميلًا مرتبطًا
            'customer_id' => ['required_if:type,delivery','nullable','integer','exists:customers,id'],
            'delivery_address_id' => ['required_if:type,delivery','integer','exists:customer_addresses,id'],
            'delivery_person_id' => ['nullable','integer','exists:delivery_persons,id'],
            'status' => ['nullable', Rule::in(['open','suspended','preparing','delivering','paid','completed','cancelled'])],
            'payment_status' => ['nullable', Rule::in(['unpaid','partial','paid'])],
            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['nullable','integer','exists:products,id'],
            'items.*.quantity' => ['required','integer','min:1'],
            'items.*.unit_price' => ['required','numeric','min:0'],
            'discount_amount' => ['nullable','numeric','min:0'],
            'delivery_charge' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string'],
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'يجب اختيار نوع الطلب',
            'type.in' => 'يجب اختيار نوع صحيح',
            'items.*.product_id.exists' => 'يجب اختيار منتج صحيح',
            'items.*.quantity.min' => 'يجب اختيار كمية صحيح',
            'items.*.unit_price.min' => 'يجب اختيار سعر صحيح',
            'discount_amount.min' => 'يجب اختيار خصم صحيح',
            'delivery_charge.min' => 'يجب اختيار رسوم توصيل صحيح',
            'notes.string' => 'يجب اختيار ملاحظات صحيح',
            'customer_id.exists' => 'يجب اختيار عميل صحيح',
            'delivery_address_id.exists' => 'يجب اختيار عنوان صحيح',
            'delivery_person_id.exists' => 'يجب اختيار مسؤول توصيل صحيح',
            'branch_id.exists' => 'يجب اختيار فرع صحيح',
            'user_id.exists' => 'يجب اختيار موظف صحيح',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'فشل التحقق من البيانات',
            'errors' => $validator->errors()
        ], 422));
    }
}
