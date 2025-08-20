<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $table = 'group_user';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'group_id',
        'is_creator',
        'status'
    ];

    public function approve(): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->save();
    }
    public function reject(): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->save();
    }
    public function pending(): void
    {
        $this->status = self::STATUS_PENDING;
        $this->save();
    }
}
