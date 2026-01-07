<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVariantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'inventory_id' => 'required|exists:inventories,id',
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('variants', 'sku'),
            ],
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:attribute_values,id',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'inventory_id.required' => 'Product is required.',
            'inventory_id.exists' => 'Selected product does not exist.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU already exists.',
            'attribute_values.required' => 'At least one attribute value is required.',
            'attribute_values.min' => 'At least one attribute value is required.',
            'attribute_values.*.exists' => 'Selected attribute value does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity cannot be negative.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
