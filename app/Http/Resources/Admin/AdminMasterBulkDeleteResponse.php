<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminMasterBulkDeleteResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => 'ok',
            'deleted' => (int) ($this['deleted'] ?? 0),
        ];
    }
}
