<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Group extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
    ];



    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(GroupUser::class)
            ->withPivot(['status', 'is_creator'])
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function message(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
