<?php

namespace App\Http\Requests\Cake;

use Illuminate\Foundation\Http\FormRequest;

class IndexCakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
