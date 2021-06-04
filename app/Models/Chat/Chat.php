<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id'];

    //RELATIONS

    /* @return BelongsTo<User> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* @return BelongsToMany<User> */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants');
    }

    /* @return HasMany<Message> */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /* @return HasOne<Message> */
    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }
}
