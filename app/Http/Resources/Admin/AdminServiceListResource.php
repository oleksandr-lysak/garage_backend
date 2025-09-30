<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminServiceListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this['items'] ?? [],
        ];
    }
}
