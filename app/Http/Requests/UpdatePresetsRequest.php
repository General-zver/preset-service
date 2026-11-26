<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePresetsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->email === 'manager@example.com';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|sometimes',
            'preset_price' => 'required|numeric|sometimes',

            'product_ids' => 'sometimes|array',
            'product_ids.*' => 'integer|distinct|exists:products,id',

            'status' => 'prohibited',
            'user_id' => 'prohibited',
        ];
    }
}
