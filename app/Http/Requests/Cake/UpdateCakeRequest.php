<?php

namespace App\Http\Requests\Cake;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0.0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }
}
