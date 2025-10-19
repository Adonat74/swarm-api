<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\EventUser;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Image;
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

        return $membership
            && $membership->status === GroupUser::STATUS_APPROVED;
    }


    /**
     * Determine whether the user can view the model.
     */
    public function addEventImages(User $user, Event $event): bool
    {
        $group = $event->group;
        $groupMembership = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();
        $eventMembership = EventUser::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();
        return $eventMembership
            && $groupMembership
            && $groupMembership->status === GroupUser::STATUS_APPROVED;
    }

    /**
     * Determine whether the user participate to event.
     */
    public function participateEvent(User $user, Event $event): bool
    {
        $group = $event->group;
        $groupMembership = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();

        return $groupMembership
            && $groupMembership->status === GroupUser::STATUS_APPROVED
            && !EventUser::where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->exists();
    }

    /**
     * Determine whether the user can leave event.
     */
    public function leaveEvent(User $user, Event $event): bool
    {
        $group = $event->group;
        $groupMembership = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();

        return $groupMembership
            && $groupMembership->status === GroupUser::STATUS_APPROVED
            && EventUser::where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function addComment(User $user, Event $event): bool
    {
        $group = $event->group;

        return GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', GroupUser::STATUS_APPROVED)
            ->exists();
    }
}
