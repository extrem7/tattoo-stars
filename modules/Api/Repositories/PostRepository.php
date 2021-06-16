<?php

namespace Modules\Api\Repositories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class PostRepository
{
    public function getForIndex(User $user): Paginator
    {
        if ($user->subscriptions()->exists()) {
            $builder = Post::whereIn('user_id', $user->subscriptions()->pluck('id'));

            if (($posts = $this->paginate($builder)) && $posts->isNotEmpty()) {
                return $posts;
            }
        }

        return $this->paginate(Post::query());
    }

    public function search(string $query): Paginator
    {
        if (preg_match('/\B(#[a-zA-Z]+\b)(?!;)/', $query)) {
            return $this->paginate(Post::where('tags', 'like', "%$query%"));
        }
        return $this->paginate(
            Post::where(
                fn($q) => $q->where('description', 'like', "%$query%")
            )
        );
    }

    public function getBookmarks(User $user): Paginator
    {
        return $this->paginate($user->bookmarks()->with('user'), false, false);
    }

    public function getPostsByUser(User $user): Paginator
    {
        $posts = $this->paginate($user->posts(), false, false);
        /* @var $posts Collection<Post>|Paginator */
        $posts->transform(function (Post $post) use ($user) {
            $post->user_id = $user->id;
            $post->setRelation('user', $user);
            return $post;
        });
        return $posts;
    }

    public function store(int $userId, string $description, string $tags = null): Post
    {
        return Post::create([
            'user_id' => $userId,
            'description' => $description,
            'tags' => $tags
        ]);
    }

    public function update(Post $post, string $description = null): bool
    {
        return $post->update(['description' => $description]);
    }

    /**
     * @param Builder|Relation|QueryBuilder $builder
     */
    protected function paginate($builder, bool $loadUsers = true, bool $hidePosts = true): Paginator
    {
        $select = ['id', 'description', 'created_at'];
        $with = [
            'imagesMedia',
            'videoMedia',
            'likes',
            'bookmarkers' => fn($q) => $q->where('user_id', '=', \Auth::id())
        ];

        if ($loadUsers) {
            $select[] = 'user_id';
            $with[] = 'user';
        }

        $user = \Auth::user();

        $notIn = [];

        if ($hidePosts) {
            $notIn[] = $user->id;

            $blockers = $user->blockers()->pluck('id');
            $blacklist = $user->blacklist()->pluck('id');

            if ($blockers->isNotEmpty()) {
                $notIn = [...$notIn, ...$blockers];
            }
            if ($blacklist->isNotEmpty()) {
                $notIn = [...$notIn, ...$blacklist];
            }
        }

        return $builder->select($select)
            ->with(array_merge($with, $builder->getEagerLoads()))
            ->withCount('comments')
            ->whereNotIn('user_id', $notIn)
            ->latest()
            ->simplePaginate(12);
    }
}
