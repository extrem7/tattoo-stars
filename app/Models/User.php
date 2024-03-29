<?php

namespace App\Models;

use App\Models\Advertising\Banner;
use App\Models\Advertising\Promotion;
use App\Models\Advertising\Top;
use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use App\Models\Post\Comment;
use App\Models\Story\Transaction;
use App\Models\Traits\Searchable;
use App\Models\User\{AccountType, Information, Style};
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasManyThrough, HasOne, MorphOne};
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Api\Notifications\VerifyEmail;
use Spatie\MediaLibrary\{HasMedia,
    InteractsWithMedia,
    MediaCollections\Exceptions\MediaCannotBeDeleted,
    MediaCollections\Models\Media
};
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory,
        HasRoles,
        HasApiTokens,
        Notifiable,
        InteractsWithMedia,
        SoftDeletes,
        CascadeSoftDeletes,
        Searchable;

    protected $fillable = [
        'account_type_id', 'name', 'nickname', 'email', 'email_verification_code', 'email_verified_at', 'password',
        'fcm_token', 'deleted_self'
    ];

    protected $hidden = ['password', 'remember_token',];

    protected $cascadeDeletes = ['posts', 'comments'];

    protected $search = ['id', 'email', 'name', 'nickname'];

    // FUNCTIONS

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
                $this->addMediaConversion('big')
                    ->width(680)
                    ->sharpen(0)
                    ->nonQueued();
                $this->addMediaConversion('medium')
                    ->width(110)
                    ->sharpen(0)
                    ->nonQueued();
                $this->addMediaConversion('small')
                    ->width(30)
                    ->sharpen(0)
                    ->nonQueued();
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

    public function hasVotedToday(): bool
    {
        return $this->votes()->whereDate('date', '=', today())->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->id === 1 || $this->email === config('admin.initial_user_email');
    }

    public function routeNotificationForFcm(): string
    {
        return $this->fcm_token;
    }

    // RELATIONS

    /* @return BelongsTo<AccountType>|AccountType */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    /* @return HasOne<Information>|Information */
    public function information(): HasOne
    {
        return $this->hasOne(Information::class);
    }

    /* @return BelongsToMany<Style>|Style */
    public function styles(): BelongsToMany
    {
        return $this->belongsToMany(Style::class, 'user_has_styles');
    }

    /* @return MorphOne<Media>|Media */
    public function avatarMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')->where('collection_name', 'avatar');
    }

    /* @return HasMany<Post>|Post */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /* @return HasMany<Comment>|Comment */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /* @return HasManyThrough<Story>|Story */
    public function stories(): HasManyThrough
    {
        return $this->hasManyThrough(Story::class, Post::class);
    }

    /* @return HasMany<Transaction>|Transaction */
    public function storyTransitions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /* @return HasManyThrough<ContestWork>|ContestWork */
    public function contestWorks(): HasManyThrough
    {
        return $this->hasManyThrough(ContestWork::class, Post::class);
    }

    /* @return BelongsToMany<ContestWork>|ContestWork */
    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(ContestWork::class, 'contest_votes', null, 'work_id');
    }

    /* @return BelongsToMany<self>|self */
    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class, 'subscriptions', 'subscriber_id', 'user_id'
        )->withPivot('subscribed_at');
    }

    /* @return BelongsToMany<self>|self */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class, 'subscriptions', 'user_id', 'subscriber_id'
        )->withPivot('subscribed_at');
    }

    /* @return BelongsToMany<Post>|Post */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes');
    }

    /* @return BelongsToMany<Story>|Story */
    public function marks(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'story_marks');
    }

    /* @return BelongsToMany<Story>|Story */
    public function views(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'story_views');
    }

    /* @return BelongsToMany<Post>|Post */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_bookmarks');
    }

    /* @return BelongsToMany<self>|self */
    public function blacklist(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class, 'blacklist', 'user_id', 'blocked_id'
        )->withPivot('blocked_at');
    }

    /* @return BelongsToMany<self>|self */
    public function blockers(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class, 'blacklist', 'blocked_id', 'user_id'
        )->withPivot('blocked_at');
    }

    /* @return BelongsToMany<Chat>|Chat */
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_participants');
    }

    /* @return BelongsToMany<Chat>|Chat */
    public function deletedChats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chats_deleted');
    }

    /* @return HasMany<Message>|Message */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /* @return Builder<Message>|Message */
    public function incomeMessages(): Builder
    {
        return Message::whereIn('chat_id', $this->chats()->pluck('id'))->where('user_id', '!=', $this->id);
    }

    /* @return HasManyThrough<Promotion>|Promotion */
    public function promotions(): HasManyThrough
    {
        return $this->hasManyThrough(Promotion::class, Post::class);
    }

    /* @return HasManyThrough<HasMany>|HasMany */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    /* @return HasMany<Top>|Top */
    public function tops(): HasMany
    {
        return $this->hasMany(Top::class);
    }

    public function scopeActiveTop(Builder $builder): Builder
    {
        /* @var $q Builder<Top>|Top */
        return $builder->whereHas('tops', fn(Builder $q) => $q->active());
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
