<?php

namespace Modules\Api\Http\Resources\Notifications;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;

class NotificationResource extends JsonResource
{
    /* @var DatabaseNotification */
    public $resource;

    protected string $notificationsNamespace = 'Modules\Api\Notifications\Push';

    public static array $typesMessages = [
        'PostLiked' => 'post_liked',
        'PostCommented' => 'post_commented',
        'UserSubscribed' => 'user_subscribed',
        'AdvertisingNotification' => 'advertising_expires'
    ];

    protected array $user;

    protected string $type;

    public function __construct(DatabaseNotification $resource)
    {
        parent::__construct($resource);

        $this->user = $resource->data['user'];
        $this->type = \Str::replaceFirst($this->notificationsNamespace . '\\', '', $resource->type);
    }

    public function toArray($request = null): array
    {
        $notification = $this->resource;

        $user = $notification->data['user'];
        $post = $notification->data['post'] ?? null;

        $data = [
            'type' => $this->type,
            'text' => $this->generateMessage(),
            'date' => $notification->created_at,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'nickname' => $user['nickname'],
                'avatar' => $user['avatar'],
            ],
        ];

        if ($post) {
            $data['post'] = [
                'id' => $post['id'],
                'thumbnail' => $post['thumbnail'],
            ];
        }

        return $data;
    }

    protected function generateMessage(): string
    {
        return __($this->getTranslationKey(), ['nickname' => $this->user['nickname']]);
    }

    protected function getTranslationKey(): string
    {
        return 'tattoo.notifications.' . self::$typesMessages[$this->type];
    }
}
