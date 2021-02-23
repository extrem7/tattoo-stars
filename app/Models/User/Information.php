<?php

namespace App\Models\User;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Information extends Model
{
    public $timestamps = null;

    protected $table = 'users_information';

    protected $primaryKey = 'user_id';

    protected $guarded = ['user_id'];

    protected $fillable = [
        'gender_id', 'birthday', 'city_id', 'address', 'bio', 'phone', 'website', 'instagram', 'facebook'
    ];

    protected $hidden = ['user_id'];

    protected $dates = ['birthday'];

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
