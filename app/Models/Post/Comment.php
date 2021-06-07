<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory,
        SoftDeletes;

    public const UPDATED_AT = null;

    public $table = 'post_comments';

    protected $fillable = ['comment_id', 'user_id', 'post_id', 'text'];

    /* @return BelongsTo<self> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /* @return HasMany<self> */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class);
    }

    /* @return BelongsTo<User> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
