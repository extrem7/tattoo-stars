<?php

namespace Modules\Admin\Http\Controllers\Users;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Http\Requests\UserRequest;
use Modules\Admin\Repositories\UserRepository;

class UserController extends Controller
{
    protected UserRepository $repository;

    public function __construct()
    {
        $this->repository = app(UserRepository::class);

        $this->middleware('can:users.create')->only(['create', 'store']);
        $this->middleware('can:users.edit')->only(['edit', 'update']);
        $this->middleware('can:users.delete')->only('destroy');
        $this->middleware('can:users.trash')->only('trash');
        $this->middleware('can:users.restore')->only('restore');
        $this->middleware('can:users.force-destroy')->only('forceDestroy');
    }

    public function index(IndexRequest $request): Response
    {
        $title = 'Пользователи';
        if ($page = $request->input('page')) {
            $title .= " - страница $page";
        }
        $this->seo()->setTitle($title);

        $users = $this->repository->getUsersForIndex($request->only(['searchQuery', 'sortBy', 'sortDesc']));

        $this->repository->transformUsers($users);

        return inertia('Users/Index', [
            'resource' => 'users',
            'data' => $users,
            'table' => [
                'searchQuery' => $request->input('searchQuery'),
                'sortBy' => $request->input('sortBy'),
                'sortDesc' => $request->input('sortDesc')
            ]
        ]);
    }

    public function create(): Response
    {
        $this->seo()->setTitle('Создать пользователя');

        return inertia('Users/Profile', [
            'accountTypes' => $this->repository->getAccountTypes(),
            'roles' => $this->repository->getRoles()
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $password = \Hash::make($request->input('password'));

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

    public function edit(int $id): Response
    {
        $user = $this->repository->findById($id);

        $this->seo()->setTitle('Редактировать пользователя');

        $data = array_merge(
            $user->only(['id', 'account_type_id', 'name', 'nickname', 'email', 'styles']),
            $user->information->load(['gender', 'city.country'])->toArray()
        );
        $data['roles'] = $user->roles->pluck('id');

        if ($user->avatarMedia) {
            $data['avatar'] = $user->avatar;
        }

        return inertia('Users/Profile', [
            'user' => $data,
            'accountTypes' => $this->repository->getAccountTypes(),
            'roles' => $this->repository->getRoles()
        ]);
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $user = $this->repository->findById($id);

        $data = $request->only(['email', 'name', 'nickname', 'account_type_id']);
        if ($password = $request->password) {
            $data['password'] = \Hash::make($password);
        }
        $user->update($data);

        $user->syncRoles($request->roles);

        return back()->with(['message' => "Пользователь {$request->name} был отредактирован."]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = $this->repository->findById($id);

        if ($user->isSuperAdmin()) {
            return back()->with(['error' => 'Ты в своем уме?']);
        }

        try {
            $user->delete();
        } catch (\Exception $e) {
            return back()->with(['error' => 'Возникла ошибка при удалении пользователя.']);
        }

        return back()->with(['message' => 'Пользователь был удален.', 'type' => 'destroy']);
    }

    public function trash(IndexRequest $request): Response
    {
        $title = 'Удаленные пользователи';
        if ($page = $request->input('page')) {
            $title .= " - страница $page";
        }
        $this->seo()->setTitle($title);

        /* @var $q SoftDeletes */
        $users = $this->repository->getUsersForIndex(
            $request->only(['searchQuery', 'sortBy', 'sortDesc']), fn(Builder $q) => $q->onlyTrashed()
        );

        $this->repository->transformUsers($users);

        return inertia('Users/Index', [
            'resource' => 'users',
            'data' => $users,
            'isTrash' => true,
            'table' => [
                'searchQuery' => $request->input('searchQuery'),
                'sortBy' => $request->input('sortBy'),
                'sortDesc' => $request->input('sortDesc')
            ]
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $user = $this->repository->findById($id);
        if ($user->trashed()) {
            $user->restore();
        }
        return back()->with(['message' => "Пользователь {$user->name} был восстановлен."]);
    }

    public function forceDestroy(int $id): RedirectResponse
    {
        $user = $this->repository->findById($id);
        if ($user->trashed()) {
            $user->forceDelete();
        }
        return back()->with(['message' => 'Пользователь был удален окончательно.', 'type' => 'destroy']);
    }
}
