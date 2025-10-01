<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Exception;

class GroupUserService
{
    public function requestToJoin(Group $group, User $user): GroupUser
    {
        $existingPivot = $group->users()->where('user_id', $user->id)->first();

        if ($existingPivot) {
            $pivot = $existingPivot->pivot;

            if ($pivot->isInvited()) {
                throw new Exception('You are already invited by the creator of the group');
            }

            if ($pivot->status === GroupUser::STATUS_PENDING) {
                throw new Exception('You already requested to join this group.');
            }

            if ($pivot->status === GroupUser::STATUS_APPROVED) {
                throw new Exception('You are already a member of this group.');
            }

            return $pivot;
        }

        // Create the pivot row
        $group->users()->attach($user->id, [
            'status' => GroupUser::STATUS_PENDING,
            'invited' => false,
            'is_creator' => false
        ]);

        return $group->users()->where('user_id', $user->id)->first()->pivot;
    }
}
