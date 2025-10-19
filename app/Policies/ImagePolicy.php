<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\EventUser;
use App\Models\GroupUser;
use App\Models\Image;
use App\Models\User;

class ImagePolicy
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
    public function deleteImage(User $user, Image $image): bool
    {
        $ownImage = $image->owner_id === $user->id;

        return $ownImage;
    }
}
