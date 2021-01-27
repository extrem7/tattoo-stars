<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    public $timestamps = null;

    protected $fillable = ['name'];

    public function getLabelAttribute()
    {
        return __('tattoo.account_types.' . $this->name) ?? $this->name;
    }
}
