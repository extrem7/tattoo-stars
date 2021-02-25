<?php

namespace App\Models\Api;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'body'];
    protected $table = 'api_pages';
    protected $fillable = ['title', 'body'];
}
