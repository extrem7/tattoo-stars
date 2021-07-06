<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Znck\Eloquent\Relations\BelongsToThrough as BelongsToThroughRelation;
use Znck\Eloquent\Traits\BelongsToThrough;

class Story extends Model
{
    use BelongsToThrough;

    public const UPDATED_AT = null;

    protected $fillable = ['post_id', 'rating'];

    //RELATIONS

    /* @return BelongsTo<Post> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsToThroughRelation
    {
        return $this->belongsToThrough(User::class, Post::class);
    }

    /* @return BelongsToMany<User> */
    public function views(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'story_views');
    }

    /* @return BelongsToMany<User> */
    public function likes(): BelongsToMany
    {
        return $this->marks()->whereNull('is_dislike');
    }

    /* @return BelongsToMany<User> */
    public function marks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'story_marks');
    }

    /* @return BelongsToMany<User> */
    public function dislikes(): BelongsToMany
    {
        return $this->marks()->whereNotNull('is_dislike');
    }

    //SCOPES
    public function scopeDaily(Builder $query): Builder
    {
        return $query->whereDate('created_at', '=', today());
    }

    public function scopeYesterday(Builder $query): Builder
    {
        return $query->whereDate('created_at', '=', today()->addDays(-1));
    }
}
