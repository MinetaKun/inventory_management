<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'product_label' => 'required|string|max:255',
            'sku_ref' => ['required', 'string', 'max:100', Rule::unique('inventories')->ignore($this->inventory)],
            'category_id' => 'required|exists:categories,id',
            'weight' => 'nullable|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }
}
