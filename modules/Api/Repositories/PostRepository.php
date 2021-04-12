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
        return $this->paginate($user->posts());
    }

    /**
     * @param Builder|Relation|QueryBuilder $builder
     */
    protected function paginate($builder): Paginator
    {
        return $builder->select(['id', 'description', 'created_at'])
            ->with(['imagesMedia', 'videoMedia'])
            ->latest()
            ->simplePaginate(12);
    }
}
