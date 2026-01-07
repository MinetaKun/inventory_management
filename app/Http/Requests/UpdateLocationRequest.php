<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('locations')->ignore($this->location)],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ];
    }
}
