<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
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
        $tenYearsAgo = Carbon::now()->subYears(10)->format('Y-m-d');
        $longTimeAgo = Carbon::now()->subYears(120)->format('Y-m-d');
        return [
//            'phone' => ['required|numeric|digits_between:10,11', 'unique:users,phone'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'avatar' => ['nullable'],
            'id_card' => ['sometimes'],
            'birth_date' => ['required', 'date' ,'before:'.$tenYearsAgo,'after:'.$longTimeAgo],
            'password' => ['sometimes'],
            'balance' => ['numeric','min:0']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
