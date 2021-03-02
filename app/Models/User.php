<?php

namespace App\Models;

use App\Models\Traits\SearchTrait;
use App\Models\User\AccountType;
use App\Models\User\Information;
use App\Models\User\Style;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Api\Notifications\VerifyEmail;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\MediaCannotBeDeleted;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory,
        HasRoles,
        HasApiTokens,
        Notifiable,
        InteractsWithMedia,
        SoftDeletes,
        SearchTrait;

    protected $fillable = [
        'account_type_id', 'name', 'nickname', 'email', 'email_verification_code', 'email_verified_at', 'password',
        'deleted_self'
    ];

    protected $hidden = ['password', 'remember_token',];

    protected $search = ['id', 'email', 'name'];

    // FUNCTIONS
    public static function boot(): void
    {
        parent::boot();

        static::created(fn(self $u) => $u->information()->create());
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->update(['email_verification_code' => random_int(1111, 9999)]);
        $this->notify(new VerifyEmail());
    }

    public function markEmailAsVerified(): bool
    {
        if ($this->forceFill(['email_verified_at' => $this->freshTimestamp()])->save()) {
            return $this->update(['email_verification_code' => null]);
        }
        return false;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('icon')
                    ->width(72)
                    ->sharpen(0)
                    ->nonQueued();
            });
    }

    public function uploadAvatar(UploadedFile $image = null): bool
    {
        if ($this->avatarMedia) {
            try {
                $this->deleteMedia($this->avatarMedia);
            } catch (MediaCannotBeDeleted $e) {
                \Log::error('Avatar cannot be deleted: ' . $e->getMessage());
            }
        }

        return $this->addMedia($image)->toMediaCollection('avatar') !== null;
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

    public function loadInfo(): self
    {
        $this->load('information.city', 'styles', 'avatarMedia');
        $this->append('icon');
        return $this;
    }

    public function isSuperAdmin(): bool
    {
        return $this->id === 1 || $this->email === config('admin.initial_user_email');
    }

    // RELATIONS
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function information(): HasOne
    {
        return $this->hasOne(Information::class);
    }

    public function styles(): BelongsToMany
    {
        return $this->belongsToMany(Style::class, 'user_has_styles');
    }

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
