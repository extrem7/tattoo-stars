<?php

namespace Modules\Api\Repositories;

use App\Models\Post;
use App\Models\Story;
use Illuminate\Contracts\Pagination\Paginator;

class StoryRepository
{
    /* @return Paginator<Story> */
    public function getForIndex(): Paginator
    {
        return Story::daily()
            ->with([
                'post.user',
                'marks' => fn($q) => $q->where('user_id', '=', \Auth::id())->withPivot('is_dislike'),
                'views' => fn($q) => $q->where('story_views.user_id', '=', \Auth::id())
            ])
            ->withCount(['likes', 'dislikes'])
            ->latest()
            ->simplePaginate(20);
    }

    public function store(Post $post): Story
    {
        return $post->stories()->create();
    }
}
