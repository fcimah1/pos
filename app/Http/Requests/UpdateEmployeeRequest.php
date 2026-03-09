<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
            'designation_id' => 'nullable|exists:designations,id',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يجب إدخال الاسم',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'البيانات المدخلة غير صحيحة',
            'errors' => $validator->errors()
        ], 422));
    }
}
