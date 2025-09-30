<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminServiceDeletePreviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'service' => $this['service'] ?? null,
            'affected_masters_count' => (int) ($this['affected_masters_count'] ?? 0),
            'masters_to_detach' => $this['masters_to_detach'] ?? [],
            'masters_to_delete' => $this['masters_to_delete'] ?? [],
        ];
    }
}
