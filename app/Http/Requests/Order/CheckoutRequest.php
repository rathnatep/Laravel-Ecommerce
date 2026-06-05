<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone'      => ['required', 'string', 'max:20'],
            'address_kh' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'address_kh.required' => 'សូមបញ្ចូលអាសយដ្ឋានរបស់អ្នក។',
        ];
    }
}
