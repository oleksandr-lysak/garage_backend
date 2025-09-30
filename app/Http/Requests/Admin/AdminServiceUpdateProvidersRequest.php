<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminServiceUpdateProvidersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'master_ids' => ['required', 'array'],
            'master_ids.*' => ['integer', 'min:1'],
        ];
    }
}
