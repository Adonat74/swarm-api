<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    use HasFactory, Notifiable;

    const STATUS_ACTIVE = 'active';
    const STATUS_REPORTED = 'reported';
    const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'name',
        'description',
        'date_time',
        'location',
        'status',
        'group_id',
    ];

    protected $with = ['group'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(EventUser::class)
            ->withPivot(['participate', 'is_creator'])
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
