<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            // 'store_id' => 'required',
            'name' => 'required|string',
            'description' => 'required',
            'quantity' => 'required',
            'price' => 'required',

        ];
    }
    public function messages(): array
    {
        return [
            'store_id.required' => 'A store_id is required',
            'name.required' => 'A name is required',
            'name.string' => 'A name is string',
            'description.required' => 'A description is required',
            'quantity,required' => 'A quantity is required',
            'price.required' => 'A price is required',

        ];
    }
}
