<?php

namespace App\Models\Advertising;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\MediaCannotBeDeleted;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'advertising_banners';
    protected $fillable = ['redirect_to_site', 'country_id', 'city_id', 'account_type', 'budget'];
    protected $casts = [
        'redirect_to_site' => 'bool',
        'city_id' => 'integer',
        'budget' => 'integer',
        'on_pause' => 'bool',
        'verified' => 'bool'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function uploadImage(UploadedFile $image = null): bool
    {
        if ($this->imageMedia) {
            try {
                $this->deleteMedia($this->imageMedia);
            } catch (MediaCannotBeDeleted $e) {
                \Log::error('Image cannot be deleted: ' . $e->getMessage());
            }
        }

        return $this->addMedia($image)->toMediaCollection('image') !== null;
    }

    /* @return MorphOne<Media>|Media */
    public function imageMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', 'image');
    }

    /* @return BelongsTo<User>|User */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* @return BelongsTo<Country>|Country */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /* @return BelongsTo<City>|City */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereNotNull('verified')->whereNull('on_pause')->whereRaw('views < budget');
    }
}
