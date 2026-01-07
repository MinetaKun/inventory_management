<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'variant_id' => 'required|exists:variants,id',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'nullable|exists:locations,id', // Nullable for team assignments
            'quantity' => 'required|integer|min:1',
            'due_date' => 'required_if:status,pending|nullable|date|after:today',
            'status' => 'required|in:pending,completed', // Pending for temporary issue, completed for immediate transfer
            'assign_to_type' => 'nullable|in:location,team', // Helper field to distinguish logic
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'variant_id.required' => 'Variant is required.',
            'from_location_id.required' => 'Source location is required.',
            'to_location_id.exists' => 'Destination location does not exist.',
            'quantity.min' => 'Quantity must be at least 1.',
            'due_date.required_if' => 'Due date is required for temporary issues.',
            'due_date.after' => 'Due date must be in the future.',
        ];
    }
}
