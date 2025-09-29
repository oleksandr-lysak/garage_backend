<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminReviewUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'rating' => 'numeric|min:0|max:5',
            'review' => 'nullable|string',
            'user_id' => 'integer|exists:users,id',
        ];
    }
}
