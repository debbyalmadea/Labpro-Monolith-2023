<?php

namespace App\Http\Requests;

use App\Enums\HttpStatusCodes;
use App\Http\Responses\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CheckoutBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'jumlah' => 'required|min:1|integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->route()->getPrefix() === 'api/barang') {
            throw new HttpResponseException(
                JsonResponse::error($validator->errors()->first(), HttpStatusCodes::BAD_REQUEST)
            );
        }

        parent::failedValidation($validator);
    }
}