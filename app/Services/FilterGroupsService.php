<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class FilterGroupsService
{
    public function filterUserApprovedGroups (Authenticatable $user): Authenticatable
    {
        $user->load(['groups' => function ($query) {
            $query->wherePivot('status', 'approved');
        }]);
        return $user;
    }
}
