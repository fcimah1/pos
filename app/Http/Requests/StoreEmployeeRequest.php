<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
            'designation_id' => 'nullable|exists:designations,id',
            'department_id' => 'nullable|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يجب إدخال الاسم',
            'email.required' => 'يجب إدخال البريد الإلكتروني',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'يجب إدخال كلمة المرور',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف',
            'role_id.required' => 'يجب اختيار الدور الوظيفي',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        \Illuminate\Support\Facades\Log::warning('Employee Validation Failed:', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all()
        ]);

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'البيانات المدخلة غير صحيحة',
            'errors' => $validator->errors()
        ], 422));
    }
}
