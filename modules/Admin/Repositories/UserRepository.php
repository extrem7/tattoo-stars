<?php

namespace Modules\Admin\Repositories;

use App\Models\User;
use App\Models\User\AccountType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class UserRepository
{
    public function findById(int $id): User
    {
        return User::withTrashed()->findOrFail($id);
    }

    public function getUsersForIndex(array $search, \Closure $scope = null): LengthAwarePaginator
    {
        return User::with(['avatarMedia', 'accountType', 'roles'])
            ->select(['id', 'account_type_id', 'email', 'email_verified_at', 'name', 'created_at'])
            ->when($search['searchQuery'] ?? false, fn(Builder $q) => $q->search($search['searchQuery']))
            ->when($search['sortBy'] ?? false, fn(Builder $q) => $q->orderBy($search['sortBy'], $search['sortDesc'] ?? false ? 'desc' : 'asc'))
            ->when($scope, fn($q) => $scope($q))
            ->paginate(10);
    }

    public function transformUsers(LengthAwarePaginator $users): void
    {
        /* @var $users Collection<User> */
        $users->transform(function (User $user) {
            $user->append('icon');
            $data = $user->toArray();
            $data['emailVerified'] = $user->hasVerifiedEmail();
            $data['roles'] = $user->roles->pluck('label')->join(', ');
            return $data;
        });
    }

    public function getAccountTypes(): array
    {
        $accountTypes = AccountType::all(['id', 'name'])->map(
            fn(AccountType $t) => ['value' => $t->id, 'text' => $t->name]
        );

        return array_reverse($accountTypes->toArray());
    }

    public function getRoles(): array
    {
        return collect(Role::all(['id', 'label'])
            ->map(fn(Role $r) => ['value' => $r->id, 'text' => $r->label]))
            ->values()
            ->toArray();
    }
}
