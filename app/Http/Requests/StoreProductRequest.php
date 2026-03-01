<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'unique:products,barcode'],
            'is_active' => ['boolean'],
            'variations' => ['required', 'array', 'min:1'],
            'variations.*.size_name' => ['nullable', 'string', 'max:100'],
            'variations.*.price' => ['required', 'numeric', 'min:0'],
            'variations.*.barcode' => ['nullable', 'string', 'unique:product_variations,barcode'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'التصنيف مطلوب',
            'category_id.exists' => 'التصنيف غير موجود',
            'name.required' => 'الاسم مطلوب',
            'name.string' => 'الاسم يجب أن يكون نصاً',
            'name.max' => 'الاسم يجب أن لا يتجاوز 255 حرفاً',
            'barcode.unique' => 'الباركود موجود بالفعل',
            'variations.required' => 'يجب إضافة تنويع واحد على الأقل',
            'variations.array' => 'التنويعات يجب أن تكون مصفوفة',
            'variations.min' => 'يجب إضافة تنويع واحد على الأقل',
            'variations.*.size_name.string' => 'اسم الحجم يجب أن يكون نصاً',
            'variations.*.size_name.max' => 'اسم الحجم يجب أن لا يتجاوز 100 حرف',
            'variations.*.price.required' => 'السعر مطلوب',
            'variations.*.price.numeric' => 'السعر يجب أن يكون رقماً',
            'variations.*.price.min' => 'السعر يجب أن يكون أكبر من 0',
            'variations.*.barcode.unique' => 'الباركود موجود بالفعل',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'التصنيف',
            'name' => 'الاسم',
            'barcode' => 'الباركود',
            'variations' => 'التنويعات',
            'variations.*.size_name' => 'اسم الحجم',
            'variations.*.price' => 'السعر',
            'variations.*.barcode' => 'الباركود',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'البيانات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422));
    }
}
