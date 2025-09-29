<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminReviewStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'rating' => 'required|numeric|min:0|max:5',
            'review' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
}
