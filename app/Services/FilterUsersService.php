<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class FilterUsersService
{
    public function filterUsersApprovedInGroup (Model $model): Model
    {
        $model->load(['users' => function ($query) {
            $query->wherePivot('status', 'approved');
        }]);
        return $model;
    }
}
