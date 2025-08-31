<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'username',
        'city',
        'postal_code',
        'country',
        'is_admin',
        'token_version'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = ['images'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'token_version' => $this->token_version,
            'id' => $this->id,
            'role_id' => $this->role_id,
            'is_pro' => $this->is_pro,
            'is_vip' => $this->is_vip,
        ];
    }


    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->using(GroupUser::class)
            ->withPivot(['status', 'is_creator'])
            ->withTimestamps();
    }

    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->using(EventUser::class)
            ->withPivot(['participate', 'is_creator'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
