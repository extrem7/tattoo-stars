<?php

namespace Modules\Admin\Http\Controllers\Users;

use App\Models\User\AccountType;
use Auth;
use Exception;
use Hash;
use Illuminate\Support\Collection;
use Modules\Admin\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(IndexRequest $request): Response
    {
        $title = 'Пользователи';
        if ($page = $request->input('page')) {
            $title .= " - страница $page";
        }
        $this->seo()->setTitle($title);

        $users = User::with(['avatarMedia', 'accountType', 'roles'])
            ->select(['id', 'account_type_id', 'email', 'email_verified_at', 'name', 'created_at'])
            ->when($request->get('searchQuery'), fn($q) => $q->search($request->get('searchQuery')))
            ->when($request->get('sortBy'), function (Builder $users) use ($request) {
                $users->orderBy($request->get('sortBy'));
            })
            ->when($request->get('sortDesc'), fn(Builder $q) => $q->latest())
            ->paginate(10);

        /* @var $users Collection<User> */
        $users->transform(function (User $user) {
            $user->append('icon');
            $user->accountType->append('label');
            $data = $user->toArray();
            $data['emailVerified'] = $user->hasVerifiedEmail();
            $data['roles'] = $user->roles->pluck('label')->join(', ');
            return $data;
        });

        return inertia('Users/Index', [
            'data' => $users,
            'table' => [
                'searchQuery' => $request->searchQuery,
                'sortBy' => $request->sortBy,
                'sortDesc' => $request->sortDesc
            ]
        ]);
    }

    public function create(): Response
    {
        abort_unless(Auth::user()->can('users.create'), 403);

        $this->seo()->setTitle('Создать пользователя');

        return inertia('Users/Profile', [
            'accountTypes' => $this->getAccountTypes(),
            'roles' => $this->getRoles()
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        abort_unless(Auth::user()->can('users.create'), 403);

        $password = Hash::make($request->input('password'));

        $user = User::create(
            array_merge($request->only('email', 'name', 'nickname', 'account_type_id'), compact('password')
            ));

        $user->assignRole($request->roles);
        $user->markEmailAsVerified();

        return redirect()->route('admin.users.edit', $user->id)->with([
            'message' => "Пользователь {$request->name} был создан",
            'type' => 'created'
        ]);
    }

    public function edit(User $user): Response
    {
        abort_unless(Auth::user()->can('users.edit'), 403);

        $this->seo()->setTitle('Редактировать пользователя');

        $data = $user->only(['id', 'account_type_id', 'name', 'nickname', 'email']);
        $data['roles'] = $user->roles->pluck('id');

        if ($user->avatarMedia) {
            $data['avatar'] = $user->avatar;
        }

        return inertia('Users/Profile', [
            'user' => $data,
            'accountTypes' => $this->getAccountTypes(),
            'roles' => $this->getRoles()
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()->can('users.edit'), 403);

        $data = $request->only('email', 'name', 'account_type_id');
        if ($password = $request->password) {
            $data['password'] = Hash::make($password);
        }
        $user->update($data);

        $user->syncRoles($request->roles);

        return redirect()->back()->with(['message' => "Пользователь {$request->name} был отредактирован"]);
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(Auth::user()->can('users.delete'), 403);

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with(['error' => 'Ты в своем уме?']);
        }

        try {
            $user->delete();
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => 'Возникла ошибка при удалении пользователя.']);
        }

        return redirect()->back()->with(['message' => 'Пользователь был удален.', 'type' => 'destroy']);
    }

    protected function getAccountTypes(): array
    {
        $accountTypes = AccountType::all(['id', 'name'])
            ->map(function (AccountType $t) {
                $t->append('label');
                return ['value' => $t->id, 'text' => $t->label];
            });

        return array_reverse($accountTypes->toArray());
    }

    protected function getRoles(): array
    {
        return collect(Role::all(['id', 'label'])
            ->map(fn(Role $r) => ['value' => $r->id, 'text' => $r->label]))
            ->values()
            ->toArray();
    }
}
