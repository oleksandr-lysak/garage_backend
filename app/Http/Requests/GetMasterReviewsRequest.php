<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMasterReviewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => 'integer|min:1|max:100|nullable',
        ];
    }
}
