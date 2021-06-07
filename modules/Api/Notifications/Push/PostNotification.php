<?php

namespace Modules\Api\Notifications\Push;

use App\Models\Post;
use App\Models\User;

abstract class PostNotification extends BaseNotification
{
    protected Post $post;

    public function __construct(User $user, Post $post)
    {
        parent::__construct($user);

        $this->post = $post;
    }

    public function toArray(User $notifiable): array
    {
        $post = $this->post->toArray();

        if ($video = $this->post->videoMedia) {
            $post['thumbnail'] = $video->getFullUrl('thumbnail');
        } else {
            $post['thumbnail'] = $this->post->imagesMedia()->first()->getFullUrl('thumb');
        }

        return array_merge(parent::toArray($notifiable), ['post' => $post]);
    }
}
