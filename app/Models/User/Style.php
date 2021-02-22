<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    public $table = 'user_styles';

    public $timestamps = null;

    protected $fillable = ['name'];

    protected $hidden = ['pivot'];

    public function getNameAttribute(string $name): string
    {
        return __('tattoo.styles.' . $name) ?? $name;
    }
}
