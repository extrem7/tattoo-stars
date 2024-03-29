<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    public $timestamps = null;

    protected $fillable = ['name'];

    public function getNameAttribute(string $name): string
    {
        return __('tattoo.account_types.' . $name) ?? $name;
    }
}
