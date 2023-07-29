<?php

namespace App\Http\Requests;

use App\Enums\HttpStatusCodes;
use App\Http\Responses\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_depan' => 'required|max:50',
            'nama_belakang' => 'required|max:50',
            'username' => 'required|min:3|max:255|unique:user',
            'email' => 'required|email:dns|unique:user',
            'password' => 'required|min:5|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->route()->getPrefix() === 'api/auth') {
            throw new HttpResponseException(
                JsonResponse::error($validator->errors()->first(), HttpStatusCodes::BAD_REQUEST)
            );
        }

        parent::failedValidation($validator);
    }
}