<?php

namespace Modules\Api\Services;

use App\Models\Post;
use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Modules\Api\Repositories\PostRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostService
{
    protected PostRepository $repository;

    public function __construct()
    {
        $this->repository = app(PostRepository::class);
    }

    /* @param UploadedFile[] $images */
    public function store(string $description, array $images = null, UploadedFile $video = null): Post
    {
        $tags = $description ? $this->parseTags($description) : null;

        $post = $this->repository->store(\Auth::id(), $description, $tags);

        if ($images) {
            foreach ($images as $file) {
                $this->compressImage($file);
                $post->addMedia($file)->toMediaCollection('images');
            }
        }
        if ($video) {
            $media = $post->addMedia($video)->toMediaCollection('video');
            $this->compressVideo($media);
        }

        return $post;
    }

    public function toggleLike(Post $post): bool
    {
        $changes = \Auth::user()->likes()->toggle($post);
        return !empty($changes['attached']);
    }

    public function toggleBookmark(Post $post): bool
    {
        $changes = \Auth::user()->bookmarks()->toggle($post);
        return !empty($changes['attached']);
    }

    protected function parseTags(string $description): ?string
    {
        $tags = null;

        preg_match_all("/(#\w+)/u", $description, $matches);
        if ($matches) {
            $hashtagsArray = array_count_values($matches[0]);
            $tags = implode('', array_keys($hashtagsArray));
        }

        return $tags;
    }

    protected function compressImage(UploadedFile $file): bool
    {
        $mime = str_replace('image/', '', $file->getMimeType());
        $path = $file->getPathname();

        if ($mime === 'jpeg') {
            $image = imagecreatefromjpeg($path);
            return imagejpeg($image, $path);
        }

        if ($mime === 'png') {
            $image = imagecreatefrompng($path);
            return imagepng($image, $path, 9);
        }

        return false;
    }

    protected function compressVideo(Media $media): bool
    {
        $filesystem = app(Filesystem::class);
        $path = str_replace(storage_path('app/'), '', $media->getPath());
        $tmpPath = str_replace('mp4', '.tmp.mp4', $path);
        try {
            $format = new X264('libmp3lame', 'libx264');
            $format->setKiloBitrate(1600)->setAudioChannels(2)->setAudioKiloBitrate(64);

            \FFMpeg::open($path)->export()->inFormat($format)->save($tmpPath);

            $filesystem->delete($path);
            return $filesystem->move($tmpPath, $path);
        } catch (\Exception $e) {
            return false;
        }
    }
}
