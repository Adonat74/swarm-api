<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\GroupUser;
use App\Models\User;

class CommentPolicy
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
    public function view(User $user, Comment $comment): bool
    {
        $group = $comment->event->group;

        return GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', GroupUser::STATUS_APPROVED)
            ->exists();
    }
}
