<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'department'  => ['required', 'in:men,women,kids'],
            'category'    => ['required', 'string', 'max:255'],
            'base_price'  => ['required', 'numeric', 'min:0.01'],
            'status'      => ['required', 'in:active,inactive'],
        ];
    }
}
