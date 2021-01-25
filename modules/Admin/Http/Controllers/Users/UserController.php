<?php

namespace Modules\Admin\Http\Controllers\Users;

use Modules\Admin\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Admin\Http\Requests\IndexRequest;
use Modules\Admin\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index(IndexRequest $request): Response
    {
        $title = 'Пользователи';
        if ($page = $request->input('page')) {
            $title .= " - страница $page";
        }
        $this->seo()->setTitle($title);

        $users = User::query()->select(['id', 'email', 'name', 'created_at'])
            ->when($request->get('searchQuery'), fn($q) => $q->search($request->get('searchQuery')))
            ->when($request->get('sortBy'), function (Builder $users) use ($request) {
                $users->orderBy($request->get('sortBy'));
            })
            ->when($request->get('sortDesc'), fn(Builder $q) => $q->latest())
            ->paginate(10);

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
        $this->seo()->setTitle('Создать пользователя');

        return inertia('Users/Profile');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $password = \Hash::make($request->input('password'));

        $user = User::create(array_merge($request->only('email', 'name'), compact('password')));

        //$user->assignRole($request->input('role'));

        return redirect()->route('admin.users.edit', $user->id)->with([
            'message' => "Пользователь {$request->name} был создан",
            'type' => 'created'
        ]);
    }

    public function edit(User $user): Response
    {
        $this->seo()->setTitle('Редактировать пользователя');

        $data = $user->only(['id', 'name', 'email']);

        if ($user->avatarMedia) {
            $data['avatar'] = $user->avatar;
        }

        return inertia('Users/Profile', [
            'user' => $data
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->only('email', 'name');
        if ($password = $request->password) {
            $data['password'] = \Hash::make($password);
        }
        $user->update($data);

        // $user->syncRoles($request->input('role'));

        return redirect()->back()->with(['message' => "Пользователь {$request->name} был отредактирован"]);
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $user->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Возникла ошибка при удалении пользователя.');
        }
        return redirect()->back()->with(['message' => 'Пользователь был удален.', 'type' => 'destroy']);
    }
}
