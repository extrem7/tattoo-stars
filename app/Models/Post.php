<?php

namespace App\Models;

use App\Models\Post\Comment;
use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $fillable = ['user_id', 'description', 'tags'];

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

    /* @return  MorphMany<Media> */
    public function imagesMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')->where('collection_name', 'images');
    }

    public function videoMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', 'video');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes');
    }

    public function bookmarkers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_bookmarks');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }
}
