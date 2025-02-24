<?php

namespace App\Http\Requests\Cake\Subscriber;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('subscribers')->where(function ($query) {
                    return $query->where('cake_id', $this->route('cake')->id);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already subscribed to this cake.',
        ];
    }
}
