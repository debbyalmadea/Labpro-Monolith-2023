<?php

namespace App\Http\Requests;

use App\Enums\HttpStatusCodes;
use App\Http\Responses\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email:dns',
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