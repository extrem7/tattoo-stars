<?php

namespace Modules\Api\Http\Controllers;

use App\Models\Post;
use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Modules\Api\Http\Requests\PostRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostController extends Controller
{
    /**
     * @api {post} /posts/create Create post
     * @apiName CreatePost
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiParam {File} images[] Array of images 500x500 less 2mb. May contains to 6 files.
     * @apiParam {File} Video no longer that 30 second and less 10mb.
     * @apiParam {String} [description] Post description.
     *
     * @apiSuccess {String} message Is post published message.
     * @apiSuccess {Number} id Created post id.
     */
    public function store(PostRequest $request): JsonResponse
    {
        $description = $request->input('description');
        $tags = $description ? $this->parseTags($description) : null;
        /* @var $post Post */
        $post = \Auth::user()->posts()->create(compact('description', 'tags'));

        if ($images = $request->file('images')) {
            foreach ($request->file('images') as $file) {
                $this->compressImage($file);
                $post->addMedia($file)->toMediaCollection('images');
            }
        }

        if ($video = $request->file('video')) {
            $media = $post->addMedia($video)->toMediaCollection('video');
            $this->compressVideo($media);
        }

        return response()->json([
            'message' => 'Post has been published.',
            'id' => $post->id
        ], 201);
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
            return imagejpeg($image, $path, 30);
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

    /**
     * @api {delete} /posts/:id Delete own post
     * @apiName DeletePost
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Is post deleted message.
     */
    public function destroy(Post $post): JsonResponse
    {
        abort_unless($post->user_id === \Auth::id(), 403, __('Your are not allowed to delete this post.'));

        $post->forceDelete();

        return response()->json([
            'message' => 'Post has been deleted.'
        ]);
    }
}
