<?php

namespace Modules\Admin\Http\Controllers\Users;

use Auth;
use Hash;
use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Admin\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $this->seo()->setTitle('Редактировать профиль');

        $user = Auth::user();

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
            'isProfile' => true
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $data = $request->only('email', 'name');
        if ($password = $request->password) {
            $data['password'] = Hash::make($password);
        }

        Auth::user()->update($data);

        return redirect()->back()->with(['message' => 'Ваш профиль был отредактирован.']);
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        return app(AvatarController::class)->update($request, Auth::user());
    }

    public function destroyAvatar(): RedirectResponse
    {
        return app(AvatarController::class)->destroy(Auth::user());
    }
}
