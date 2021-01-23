<?php

namespace Modules\Admin\Http\Controllers;

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

        return inertia('Users', [
            'data' => $users,
            'table' => [
                'searchQuery' => $request->searchQuery,
                'sortBy' => $request->sortBy,
                'sortDesc' => $request->sortDesc
            ]
        ]);
    }

    public function create()
    {
        $this->seo()->setTitle('Create a new user');

        $this->userService->shareForCRUD();

        return $this->form();
    }

    public function store(UserRequest $request)
    {
        $this->seo()->setTitle('Edit a user');

        $password = Hash::make($request->input('password'));

        $user = User::create(array_merge($request->only('email', 'name', 'type'), compact('password')));

        $user->assignRole($request->input('role'));

        if ($user->is_volunteer) {
            $user->volunteer()->create();
        }

        return response()->json([
            'status' => 'User has been created',
            'title' => $this->seo()->getTitle(),
            'user' => $user
        ], 201);
    }

    public function edit(User $user)
    {
        $this->seo()->setTitle('Edit a user');

        $this->userService->shareForCRUD();

        $user->role = $user->roles()->first()->id ?? null;

        share(compact('user'));

        return $this->form();
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->only('email', 'name', 'type');
        if ($password = $request->input('password')) {
            $data['password'] = Hash::make($password);
        }
        $user->update($data);

        $user->syncRoles($request->input('role'));

        return response()->json(['status' => 'User has been updated', 'user' => $user]);
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
