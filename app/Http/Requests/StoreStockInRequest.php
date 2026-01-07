<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockInRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'variant_id' => 'required|exists:variants,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'variant_id.required' => 'Variant is required.',
            'variant_id.exists' => 'Selected variant does not exist.',
            'location_id.required' => 'Location is required.',
            'location_id.exists' => 'Selected location does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
