<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $table = 'group_user';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'user_id',
        'group_id',
        'is_creator',
        'status',
        'invited'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function isCreator(): bool
    {
        return $this->is_creator;
    }

    public function isInvited(): bool
    {
        return $this->invited;
    }
    public function isRequest(): bool
    {
        return !$this->invited;
    }
}
