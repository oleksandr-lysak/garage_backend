<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ClientResource
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 */
class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
        ];
    }
}
