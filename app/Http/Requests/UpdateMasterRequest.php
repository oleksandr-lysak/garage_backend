<?php

namespace App\Http\Requests;

use App\Rules\Base64Image;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMasterRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [
            'contact_phone' => ['nullable', 'regex:/^\+?380\d{9}$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'service_id' => ['nullable', 'exists:services,id'],
            'age' => ['nullable', 'integer', 'between:18,99'],
            'photo' => ['nullable', new Base64Image],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
