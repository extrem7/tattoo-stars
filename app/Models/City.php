<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    public $incrementing = false;

    public $timestamps = null;

    protected $fillable = ['id', 'country_id', 'name', 'name_ru', 'lat', 'lng', 'population'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeBiggest(Builder $query): Builder
    {
        return $query->orderByDesc('population');
    }

    public function getNameAttribute(string $name = null): string
    {
        return app()->getLocale() === 'ru' ? $this->name_ru : $name;
    }
}
