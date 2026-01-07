<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255|unique:attribute_values,value,NULL,id,attribute_id,' . $this->attribute_id,
            'status' => 'required|in:active,inactive',
        ];
    }
}
