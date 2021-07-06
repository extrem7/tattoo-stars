<?php

namespace App\Models\Story;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const CREATED_AT = 'date';
    public const UPDATED_AT = null;

    protected $table = 'story_transactions';

    protected $fillable = ['amount', 'balance', 'daily'];

    protected $casts = ['daily' => 'boolean'];

    /* @return BelongsTo<User>|User */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
