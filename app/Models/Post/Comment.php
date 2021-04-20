<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory,
        SoftDeletes;

    public const UPDATED_AT = null;

    public $table = 'post_comments';

    protected $fillable = ['user_id', 'post_id', 'text'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
