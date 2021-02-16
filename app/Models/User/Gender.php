<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    public $timestamps = null;
    protected $table = 'user_genders';
    protected $fillable = ['name'];

    public function getNameAttribute(string $name): string
    {
        return __('tattoo.genders.' . $name) ?? $name;
    }
}
