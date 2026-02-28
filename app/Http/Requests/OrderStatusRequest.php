<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // مؤقتاً للعمل بدون مصادقة
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:open,suspended,paid,completed,cancelled'],
        ];
    }
}
