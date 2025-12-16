<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        if (!$this->filled('password')) {
            $this->request->remove('password');
        }
        if (!$this->filled('id_card')) {
            $this->request->remove('id_card');
        }
        if (!$this->filled('avatar')) {
            $this->request->remove('avatar');
        }
        if (!$this->has('balance') || $this->balance === null) {
            $this->merge([
                'balance' => 0,
            ]);
        }

        return [
            'phone' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'avatar' => ['nullable'],
            'id_card' => ['sometimes'],
            'birth_date' => ['required', 'date'],
            'password' => ['sometimes'],
            'balance' => ['numeric']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
