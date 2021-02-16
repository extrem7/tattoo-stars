<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    public $incrementing = false;

    public $timestamps = null;

    protected $fillable = ['id', 'country_id', 'name', 'lat', 'lng', 'population'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeBiggest(Builder $query): Builder
    {
        return $query->orderByDesc('population');
    }
}
