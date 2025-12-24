<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => 'required',
        ];
    }

    public function authorize(): bool
    {
        if(request()->user()->id === $this->route('message')->sender_id){
            return true;
        }
        return false;
    }
}
