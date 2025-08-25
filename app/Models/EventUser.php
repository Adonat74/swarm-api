<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventUser extends Pivot
{
    protected $table = 'event_user';



    protected $fillable = [
        'event_id',
        'user_id',
        'participate',
        'is_creator'
    ];
}
