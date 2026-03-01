<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
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
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'اسم العميل مطلوب',
            'customer_name.max' => 'اسم العميل يجب ألا يتجاوز 255 حرفًا',
            'customer_phone.required' => 'رقم العميل مطلوب',
            'customer_phone.max' => 'رقم العميل يجب ألا يتجاوز 20 حرفًا',
            'items.required' => 'المنتجات مطلوبة',
            'items.array' => 'المنتجات يجب أن تكون مصفوفة',
            'items.*.product_id.required' => 'معرف المنتج مطلوب',
            'items.*.product_id.exists' => 'المنتج غير موجود',
            'items.*.quantity.required' => 'الكمية مطلوبة',
            'items.*.quantity.integer' => 'الكمية يجب أن تكون عددًا صحيحًا',
            'items.*.quantity.min' => 'الكمية يجب أن تكون 1 على الأقل',
            'items.*.price.required' => 'السعر مطلوب',
            'items.*.price.numeric' => 'السعر يجب أن يكون رقمًا',
            'items.*.price.min' => 'السعر يجب أن يكون 0 على الأقل',
            'items.*.size.required' => 'المقاس مطلوب',
            'items.*.size.max' => 'المقاس يجب ألا يتجاوز 50 حرفًا',
            'total_amount.required' => 'المبلغ الإجمالي مطلوب',
            'total_amount.numeric' => 'المبلغ الإجمالي يجب أن يكون رقمًا',
            'total_amount.min' => 'المبلغ الإجمالي يجب أن يكون 0 على الأقل',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
            'branch_id.required' => 'معرف الفرع مطلوب',
            'branch_id.exists' => 'الفرع غير موجود',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name' => 'اسم العميل',
            'customer_phone' => 'رقم العميل',
            'items' => 'المنتجات',
            'items.*.product_id' => 'معرف المنتج',
            'items.*.quantity' => 'الكمية',
            'items.*.price' => 'السعر',
            'items.*.size' => 'المقاس',
            'total_amount' => 'المبلغ الإجمالي',
            'payment_method' => 'طريقة الدفع',
            'branch_id' => 'معرف الفرع',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'البيانات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422));
    }
}
