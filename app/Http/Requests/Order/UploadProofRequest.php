<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UploadProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5 MB — no SVG
        ];
    }

    public function messages(): array
    {
        return [
            'payment_proof.required' => 'Please select your payment screenshot.',
            'payment_proof.image'    => 'The file must be an image (jpg, png, etc.).',
            'payment_proof.max'      => 'The image must not exceed 5 MB.',
        ];
    }
}
