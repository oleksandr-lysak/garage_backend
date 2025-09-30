<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminServiceDeleteResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => 'ok',
            'deleted_service_id' => (int) ($this['deleted_service_id'] ?? 0),
            'detached_from_masters' => (int) ($this['detached_from_masters'] ?? 0),
            'deleted_masters' => (int) ($this['deleted_masters'] ?? 0),
        ];
    }
}
