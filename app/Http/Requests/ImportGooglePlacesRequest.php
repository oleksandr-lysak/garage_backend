<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportGooglePlacesRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow anyone or adjust auth here if needed
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => 'sometimes|integer|min:1|max:10000',
        ];
    }
}
