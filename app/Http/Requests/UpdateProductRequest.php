<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', Rule::unique('products', 'barcode')->ignore($productId)],
            'is_active' => ['boolean'],
            'variations' => ['required', 'array', 'min:1'],
            'variations.*.id' => ['nullable', 'exists:product_variations,id'],
            'variations.*.size_name' => ['nullable', 'string', 'max:100'],
            'variations.*.price' => ['required', 'numeric', 'min:0'],
            'variations.*.barcode' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Category does not exist',
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name cannot exceed 255 characters',
            'barcode.required' => 'Barcode is required',
            'barcode.string' => 'Barcode must be a string',
            'barcode.unique' => 'Barcode already exists',
            'is_active.required' => 'Is active is required',
            'is_active.boolean' => 'Is active must be a boolean',
            'variations.required' => 'Variations are required',
            'variations.array' => 'Variations must be an array',
            'variations.min' => 'Variations must have at least one item',
            'variations.*.id.required' => 'Variation ID is required',
            'variations.*.id.exists' => 'Variation ID does not exist',
            'variations.*.size_name.required' => 'Variation size name is required',
            'variations.*.size_name.string' => 'Variation size name must be a string',
            'variations.*.size_name.max' => 'Variation size name cannot exceed 100 characters',
            'variations.*.price.required' => 'Variation price is required',
            'variations.*.price.numeric' => 'Variation price must be a number',
            'variations.*.price.min' => 'Variation price cannot be negative',
            'variations.*.barcode.required' => 'Variation barcode is required',
            'variations.*.barcode.string' => 'Variation barcode must be a string',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'Category',
            'name' => 'Name',
            'barcode' => 'Barcode',
            'is_active' => 'Is Active',
            'variations' => 'Variations',
            'variations.*.id' => 'Variation ID',
            'variations.*.size_name' => 'Variation Size Name',
            'variations.*.price' => 'Variation Price',
            'variations.*.barcode' => 'Variation Barcode',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $variations = $this->input('variations', []);
            $barcodes = [];

            foreach ($variations as $index => $variation) {
                // Check for duplicate barcodes within the same request
                if (isset($variation['barcode']) && $variation['barcode']) {
                    if (in_array($variation['barcode'], $barcodes)) {
                        $validator->errors()->add("variations.{$index}.barcode", 'Duplicate barcode found within variations');
                    }
                    $barcodes[] = $variation['barcode'];
                }

                // Check for duplicate size_name within the same request
                $sizeNames = array_column(array_filter($variations, fn($v) => isset($v['size_name'])), 'size_name');
                if (isset($variation['size_name']) && $variation['size_name']) {
                    $count = count(array_filter($sizeNames, fn($name) => $name === $variation['size_name']));
                    if ($count > 1) {
                        $validator->errors()->add("variations.{$index}.size_name", 'Duplicate size name found within variations');
                    }
                }
            }
        });
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'البيانات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422));
    }
}
