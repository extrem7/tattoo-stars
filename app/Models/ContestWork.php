<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContestWork extends Model
{
    public const CREATED_AT = 'date';
    public const UPDATED_AT = null;

    protected $table = 'contest';

    protected $fillable = ['post_id', 'rating'];

    protected $casts = ['winner' => 'bool'];

    //RELATIONS

    /* @return BelongsTo<Post> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contest_votes', 'work_id');
    }

    //SCOPES
    public function scopeDaily(Builder $query): Builder
    {
        return $query->whereDate('date', today());
    }

    public function scopeYesterday(Builder $query): Builder
    {
        return $query->whereDate('date', '=', today()->addDays(-1));
    }
}
