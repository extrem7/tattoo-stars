<?php

namespace Modules\Api\Http\Resources\Notifications;

use Illuminate\Notifications\DatabaseNotification;

class PostCommentedNotification extends NotificationResource
{
    protected array $comment;

    public function __construct(DatabaseNotification $resource)
    {
        parent::__construct($resource);

        $this->comment = $resource->data['comment'];
    }

    public function toArray($request = null): array
    {
        return array_merge(parent::toArray(), ['comment' => $this->comment]);
    }

    protected function generateMessage(): string
    {
        return __($this->getTranslationKey(), [
            'nickname' => $this->user['nickname'],
            'comment' => $this->comment['text']
        ]);
    }
}
