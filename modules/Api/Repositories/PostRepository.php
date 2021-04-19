<?php

namespace Modules\Api\Repositories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;

class PostRepository
{
    public function getForIndex(User $user): Paginator
    {
        if ($user->subscriptions()->exists()) {
            $builder = Post::whereIn('user_id', $user->subscriptions()->pluck('id'));

            if (($posts = $this->paginate($builder)) && $posts->hasPages()) {
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

    public function getPostsByUser(User $user): Paginator
    {
        $posts = $this->paginate($user->posts(), false);
        $posts->transform(function (Post $post) use ($user) {
            $post->user_id = $user->id;
            $post->setRelation('user', $user);
            return $post;
        });
        return $posts;
    }

    /**
     * @param Builder|Relation|QueryBuilder $builder
     */
    protected function paginate($builder, bool $loadUsers = true): Paginator
    {
        $select = ['id', 'description', 'created_at'];
        $with = ['imagesMedia', 'videoMedia', 'likes'];

        if ($loadUsers) {
            $select[] = 'user_id';
            $with[] = 'user';
        }

        return $builder->select($select)
            ->with($with)
            ->latest()
            ->simplePaginate(12);
    }
}
