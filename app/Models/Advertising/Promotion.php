<?php

namespace App\Models\Advertising;

use App\Models\City;
use App\Models\Country;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    public static $accountTypes = [
        'users' => 'Пользователям',
        'other' => 'Мастерам и студиям',
        null => 'Всем'
    ];
    protected $table = 'advertising_promotions';
    protected $fillable = ['post_id', 'country_id', 'city_id', 'account_type', 'budget'];
    protected $casts = [
        'post_id' => 'integer',
        'city_id' => 'integer',
        'budget' => 'integer',
        'on_pause' => 'bool',
        'verified' => 'bool'
    ];

    /* @return BelongsTo<Post>|Post */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /* @return BelongsTo<Country>|Country */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /* @return BelongsTo<City>|City */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereNotNull('verified')->whereRaw('views < budget');
    }
}
