<?php

namespace App\Models;

use App\Models\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasFactory,
        Notifiable,
        InteractsWithMedia,
        SoftDeletes,
        SearchTrait;

    protected $fillable = ['name', 'email', 'password',];

    protected $hidden = ['password', 'remember_token',];

    protected $search = ['email', 'name'];

    // FUNCTIONS
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('icon')
                    ->width(110)
                    ->sharpen(0)
                    ->nonQueued();
            });
        $this->addMediaCollection('resume')->singleFile();
    }

    public function uploadAvatar(UploadedFile $image = null): void
    {
        if ($this->avatarMedia) $this->deleteMedia($this->avatarMedia);

        $this->addMedia($image)->toMediaCollection('avatar');
    }

    public function deleteAvatar(): bool
    {
        return $this->avatarMedia->delete();
    }

    public function getAvatar(string $size = ''): string
    {
        if ($this->avatarMedia !== null) {
            return $this->avatarMedia->getUrl($size);
        }

        return asset('/admin/dist/img/no-avatar.png');
    }

    // RELATIONS
    public function avatarMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', 'avatar');
    }

    // ACCESSORS
    public function getAvatarAttribute(): string
    {
        return $this->getAvatar();
    }

    public function getIconAttribute(): string
    {
        return $this->getAvatar('icon');
    }
}
