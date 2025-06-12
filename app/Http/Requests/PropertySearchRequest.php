<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertySearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'location' => 'nullable|string|max:255',
            'near_beach' => 'nullable|integer|between:0,1',
            'accepts_pets' => 'nullable|integer|between:0,1',
            'sleeps' => 'nullable|integer|min:' . config('property.min_sleeps') . '|max:' . config('property.max_sleeps'),
            'beds' => 'nullable|integer|min:' . config('property.min_beds') . '|max:' . config('property.max_beds'),
            'available_from' => 'nullable|date|after:today',
            'available_to' => 'nullable|date|after:available_from|after:tomorrow'
        ];
    }
}
