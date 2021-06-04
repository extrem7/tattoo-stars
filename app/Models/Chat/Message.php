<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'chat_messages';

    protected $fillable = ['user_id', 'text', 'media_id'];

    protected $casts = ['viewed' => 'bool'];

    //RELATIONS

    /* @return BelongsTo<User> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //SCOPES
    public function scopeNotViewed(Builder $builder): Builder
    {
        return $builder->where('viewed', '=', false);
    }
}
