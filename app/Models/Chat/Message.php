<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Message extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const UPDATED_AT = null;

    protected $table = 'chat_messages';

    protected $fillable = ['user_id', 'text', 'media_id'];

    protected $casts = ['viewed' => 'bool'];

    // FUNCTIONS
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();

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

    /* @return BelongsTo<User>|User */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* @return MorphOne<Media>|Media */
    public function imageMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', 'image');
    }

    /* @return MorphOne<Media>|Media */
    public function videoMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', 'video');
    }

    //SCOPES
    public function scopeUnread(Builder $builder): Builder
    {
        return $builder->where('viewed', '=', false);
    }
}
