<?php

namespace App\Services;

class AttachService
{
    public function attachUserModel ($model, $relatedModelIds): void
    {
        if (isset($relatedModelIds)) {
            $model->users()->attach($relatedModelIds, [
                'is_creator' => true,
                'status' => 'accepted',
            ]);
        }
    }
}
