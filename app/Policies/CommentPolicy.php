<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Event;
use App\Models\GroupUser;
use App\Models\Reaction;
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

    /**
     * Determine whether the user can view the model.
     */
    public function addCommentReply(User $user, Comment $comment): bool
    {
        $group = $comment->event->group;

        return GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', GroupUser::STATUS_APPROVED)
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function addCommentReaction(User $user, Comment $comment): bool
    {
        $reaction = Reaction::where('comment_id', $comment->id)
            ->where('user_id', $user->id)
            ->exists();

        $group = $comment->event->group;
        $groupUser = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', GroupUser::STATUS_APPROVED)
            ->exists();

        return !$reaction && $groupUser;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function deleteCommentReaction(User $user, Comment $comment): bool
    {
        $reaction = Reaction::where('comment_id', $comment->id)
            ->where('user_id', $user->id)
            ->exists();

        $group = $comment->event->group;
        $groupUser = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', GroupUser::STATUS_APPROVED)
            ->exists();

        return $reaction && $groupUser;
    }
}
