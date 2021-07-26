<?php

namespace App\Models\Advertising;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Top extends Model
{
    public $timestamps;

    protected $table = 'advertising_tops';

    protected $fillable = ['days'];

    protected $casts = [
        'days' => 'integer',
        'start_at' => 'date',
        'end_at' => 'date',
    ];

    /* @return BelongsTo<User>|User */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereNotNull('start_at')->whereDate('end_at', '>=', now());
    }
}
