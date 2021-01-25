<?php

namespace Modules\Admin\Http\Controllers\Users;

use Illuminate\Validation\ValidationException;
use Modules\Admin\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function update(Request $request, User $user): RedirectResponse
    {
        try {
            ['avatar' => $avatar] = $this->validate($request, [
                'avatar' => ['required', 'image', 'max:5096', 'mimes:jpg,jpeg,bmp,png']
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->with([
                'error' => implode(', ', $e->errorBag('default')->errors()['avatar'])
            ]);
        }


        $user->uploadAvatar($avatar);
        $user->load('avatarMedia');

        return redirect()->back();
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->deleteAvatar();
        $user->load('avatarMedia');

        return redirect()->back();
    }
}
