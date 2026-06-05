<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'size'  => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }
}
