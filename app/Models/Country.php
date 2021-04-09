<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use Searchable;

    public $incrementing = false;
    public $timestamps = null;
    protected $keyType = 'string';

    protected $fillable = ['id', 'country_id', 'name', 'name_ru', 'lat', 'lng', 'population'];

    protected $search = ['name', 'name_ru'];

    /* @return City|HasMany */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function getNameAttribute(string $name = null): string
    {
        return app()->getLocale() === 'ru' ? $this->name_ru : $name;
    }
}
