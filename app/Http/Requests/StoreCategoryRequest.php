<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCategoryRequest extends FormRequest
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
            'slug' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.string' => 'الاسم يجب أن يكون نصاً',
            'name.max' => 'الاسم يجب أن لا يتجاوز 255 حرفاً',
            'slug.required' => 'الرمز مطلوب',
            'slug.string' => 'الرمز يجب أن يكون نصاً',
            'slug.max' => 'الرمز يجب أن لا يتجاوز 255 حرفاً',
            'branch_id.required' => 'الفرع مطلوب',
            'branch_id.exists' => 'الفرع غير موجود',
            'is_active.required' => 'الحالة مطلوبة',
            'is_active.boolean' => 'الحالة يجب أن تكون قيمة منطقية',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'slug' => 'الرمز',
            'branch_id' => 'الفرع',
            'is_active' => 'الحالة',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->slug && Category::where('slug', $this->slug)->exists()) {
                $validator->errors()->add('slug', 'الرمز موجود بالفعل');
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'البيانات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422));
    }
}
