<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Exception;

class GroupPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Group $group): bool
    {
        $membership = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();

        return $membership && $membership->status === GroupUser::STATUS_APPROVED;
    }

    public function createEvent(User $user, Group $group): bool
    {
        $membership = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();

        return $membership && $membership->status === GroupUser::STATUS_APPROVED;
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): bool
    {
        if ($user->id !== $group->user_id) {
            return false;
        }
        if($group->check_in <= now()) {
            throw new Exception("It's too late to delete booking");
        }
        return true;
    }
}
