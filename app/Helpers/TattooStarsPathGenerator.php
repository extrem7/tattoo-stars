<?php

namespace App\Helpers;

use App\Models\Chat\Message;
use App\Models\Post;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class TattooStarsPathGenerator implements PathGenerator
{

    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    protected function getBasePath(Media $media): string
    {
        $folder = '';
        $useKey = true;
        $collection = $media->collection_name;

        switch ($media->model_type) {
            case Post::class:
                if ($collection === 'video') {
                    $useKey = false;
                }
                $folder = 'posts';
                break;
            case User::class:
                return 'users/' . $media->model_id . "/$collection";
            case Message::class:
                return 'messages/' . $media->model_id . "/$collection";
        }

        return "$folder/" . $media->model_id . "/$collection" . ($useKey ? "/{$media->getKey()}" : '');
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }
}
