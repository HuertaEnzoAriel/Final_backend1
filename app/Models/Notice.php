<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    public const POST_APPROVED = 'post_approved';
    public const POST_REJECTED = 'post_rejected';
    public const ROLE_CHANGED = 'role_changed';

    protected $fillable = ['user_id', 'kind', 'post_id', 'title', 'detail'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
