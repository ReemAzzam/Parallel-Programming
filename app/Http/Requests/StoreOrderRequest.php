<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
        'product_id' => 'required|exists:products,id',
        'color' => 'required|string',
        'amount' => 'required|integer|min:1'
        ];
    }
    public function messages(): array
    {
        return [
            'user_id.required' => 'A user_id is required',
            'product_id.required' => 'A product_id is required',
            'color.required' => 'A color is required',
            'amount.required' => 'A amount is required',
            'amount.integer' => 'A amount must be integer',

        ];
    }
}
