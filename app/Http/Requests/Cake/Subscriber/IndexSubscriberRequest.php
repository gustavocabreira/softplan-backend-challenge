<?php

namespace App\Http\Requests\Cake\Subscriber;

use App\Models\Subscriber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'order_by' => ['sometimes', 'string', Rule::in((new Subscriber)->getFillable())],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'name' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
