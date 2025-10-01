<?php

namespace App\Policies;

use App\Models\GroupUser;
use App\Models\User;

class GroupUserPolicy
{
    /**
     * Determine whether the user can update the status of a group membership.
     */
    public function updateStatus(User $user, GroupUser $groupUser): bool
    {


        // 2. If it's the invited user handling their own invitation
        if (
            $groupUser->status ==='pending' // update only pending
            && $groupUser->isInvited()
            && $groupUser->user_id === $user->id
        ) {
            return true;
        }

        // 3. Otherwise, forbidden
        return false;
    }
}
