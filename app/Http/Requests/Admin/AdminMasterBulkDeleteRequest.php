<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminMasterBulkDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // already under auth middleware
    }

    public function rules(): array
    {
        return [];
    }
}
