<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [

        ];
    }

    public function authorize(): bool
    {
        if(request()->user()->id ===$this->route('message')->sender_id){
            return true;
        }
        return false;
    }
}
