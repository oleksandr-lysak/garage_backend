<?php

namespace App\Policies;

use App\Models\Master;
use App\Models\User;

class MasterPolicy
{
    /**
     * Determine whether the user can update the master.
     */
    public function update(User $user, Master $master): bool
    {
        // Allow update if the authenticated user owns this master record
        return $master->user_id === $user->id;
    }
}
