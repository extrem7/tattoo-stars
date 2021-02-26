<?php

namespace App\Models\Api;

use App\Models\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTranslations;
use Spatie\EloquentSortable\Sortable;

class Faq extends Model implements Sortable
{
    use HasTranslations;
    use SortableTrait;

    public $translatable = ['question', 'answer'];
    protected $table = 'api_faqs';
    protected $fillable = ['question', 'answer', 'order'];
}
