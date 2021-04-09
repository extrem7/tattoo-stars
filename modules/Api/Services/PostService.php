<?php

namespace Modules\Api\Services;

use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostService
{
    public function parseTags(string $description): ?string
    {
        $tags = null;

        preg_match_all("/(#\w+)/u", $description, $matches);
        if ($matches) {
            $hashtagsArray = array_count_values($matches[0]);
            $tags = implode('', array_keys($hashtagsArray));
        }

        return $tags;
    }

    public function compressImage(UploadedFile $file): bool
    {
        $mime = str_replace('image/', '', $file->getMimeType());
        $path = $file->getPathname();

        if ($mime === 'jpeg') {
            $image = imagecreatefromjpeg($path);
            return imagejpeg($image, $path, 30);
        }

        if ($mime === 'png') {
            $image = imagecreatefrompng($path);
            return imagepng($image, $path, 9);
        }

        return false;
    }

    public function compressVideo(Media $media): bool
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
