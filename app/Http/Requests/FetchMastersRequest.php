<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchMastersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'string|nullable',
            'service_id' => 'integer|exists:services,id|nullable',
            'min_rating' => 'numeric|min:0|max:5|nullable',
            'available' => 'boolean|nullable',
            'min_age' => 'integer|min:0|nullable',
            'max_age' => 'integer|min:0|nullable',
            'min_price' => 'numeric|min:0|nullable',
            'max_price' => 'numeric|min:0|nullable',
            'selected_services' => 'array|nullable',
            'selected_services.*' => 'integer|exists:services,id',
            'sort_by' => 'in:rating,name,age|nullable',
            'per_page' => 'integer|min:1|max:100|nullable',
            'page' => 'integer|min:1|nullable',
        ];
    }
}
