<?php

namespace Modules\Api\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class PostRepository
{
    public function getPostsByUser(User $user): Paginator
    {
        return $user->posts()
            ->select(['id', 'description', 'created_at'])
            ->with(['imagesMedia', 'videoMedia'])
            ->latest()
            ->simplePaginate(12);
    }
}
