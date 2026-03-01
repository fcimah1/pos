<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,' . $this->category->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم التصنيف مطلوب',
            'name.string' => 'اسم التصنيف يجب أن يكون نصاً',
            'name.max' => 'اسم التصنيف يجب ألا يتجاوز 255 حرفاً',
            'slug.required' => 'الرابط المختصر مطلوب',
            'slug.string' => 'الرابط المختصر يجب أن يكون نصاً',
            'slug.max' => 'الرابط المختصر يجب ألا يتجاوز 255 حرفاً',
            'slug.unique' => 'الرابط المختصر موجود بالفعل',
            'description.string' => 'الوصف يجب أن يكون نصاً',
            'is_active.boolean' => 'الحالة يجب أن تكون قيمة منطقية',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم التصنيف',
            'slug' => 'الرابط المختصر',
            'description' => 'الوصف',
            'is_active' => 'الحالة',
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
