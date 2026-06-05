<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_size_id' => ['required', 'integer', 'exists:product_sizes,id'],
            'quantity'        => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_size_id.required' => 'Please select a size before adding to cart.',
            'product_size_id.exists'   => 'The selected size is not valid.',
        ];
    }
}
