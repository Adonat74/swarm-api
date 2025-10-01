<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Group;

class FilterUsersService
{
    public function filterUsersApprovedInGroup (Group $group): Group
    {
        $group->load(['users' => function ($query) {
            $query->wherePivot('status', 'approved');
        }]);
        return $group;
    }

    public function filterUsersParticipatingEvent (Event $event): Event
    {
        $event->load(['users' => function ($query) {
            $query->wherePivot('participate', true);
        }]);
        return $event;
    }
}
