<?php

namespace App\Http\Requests\Availability;

use App\Models\Master;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @property mixed $phone
 * @property mixed $country_code
 * @property mixed $latitude
 * @property mixed $longitude
 */
class SetAvailableMasterRequest extends FormRequest
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
            'start_time' => 'required|date',
            'duration' => 'required|integer', //in minutes
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            $id = $this->route('id');
            if (! Master::where('id', $id)->exists()) {
                $validator->errors()->add('id', 'Master not found.');
            }
        });
    }
}
