<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettleOrdersRequest extends FormRequest
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
            'driver_id' => 'required|integer|exists:delivery_persons,id',
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'driver_id.required' => 'يجب اختيار السائق',
            'driver_id.exists' => 'السائق غير موجود',
            'order_ids.required' => 'يجب اختيار الطلبات',
            'order_ids.array' => 'الطلبات يجب أن تكون مصفوفة',
            'order_ids.*.integer' => 'رقم الطلب يجب أن يكون عدد صحيح',
            'order_ids.*.exists' => 'أحد الطلبات غير موجود',
        ];
    }

    public function attributes(): array
    {
        return [
            'driver_id' => 'السائق',
            'order_ids' => 'الطلبات',
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
