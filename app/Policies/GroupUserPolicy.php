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

        if (
            $groupUser->status === GroupUser::STATUS_PENDING // update only pending
            && $groupUser->isInvited()
            && $groupUser->user_id === $user->id
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can leave the group.
     */
    public function leaveGroup(User $user, GroupUser $groupUser): bool
    {

        if (!$groupUser->isCreator()
            &&$groupUser->status === GroupUser::STATUS_APPROVED // update only approved
            && $groupUser->user_id === $user->id
        ) {
            return true;
        }

        return false;
    }
}
