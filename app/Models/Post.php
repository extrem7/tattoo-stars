<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory,
        SoftDeletes,
        InteractsWithMedia,
        Searchable;

    protected $fillable = ['description', 'tags'];

    protected $search = ['description'];

    public static function boot(): void
    {
        foreach (['creating', 'updating'] as $event) {
            static::$event(function () {
            });
        }

        parent::boot();
    }

    // FUNCTIONS
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->registerMediaConversions(function (Media $media) {
            $this->addMediaConversion('thumb')
                ->crop('crop-center', 220, 220)
                ->sharpen(0)
                ->nonQueued();
        });

        $this->addMediaCollection('video')
            ->singleFile()
            ->acceptsMimeTypes(['video/mp4'])
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumbnail')
                    ->extractVideoFrameAtSecond(2)
                    ->crop('crop-center', 500, 500)
                    ->performOnCollections('video')
                    ->nonQueued();
            });
    }

    //RELATIONS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function imagesMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')->where('collection_name', 'images');
    }

    public function videoMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', 'video');
    }
}
