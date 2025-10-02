<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;

class EventPolicy
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
    public function view(User $user, Event $event): bool
    {
        $group = $event->group;
        $membership = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();

        return $membership && $membership->status === GroupUser::STATUS_APPROVED;
    }
}
