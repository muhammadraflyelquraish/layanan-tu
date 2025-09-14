<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = [
            'name' => $request->name,
            'no_identity' => $request->no_identity,
        ];
        if ($request->old_password && $request->password) {
            if (!Hash::check($request->old_password, $user->password)) {
                return Redirect::route('profile.edit')->withErrors(['old_password' => 'Password lama tidak cocok.']);
            }

            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return Redirect::route('profile.edit')->with('status', 'Profile berhasil diupdate');
    }

    public function changeRole(Request $request)
    {
        $user = auth()->user();
        $userRoleId = $request->query('user_role_id');

        $userRole = UserRole::where('id', $userRoleId)->where('user_id', $user->id)->first();
        if ($userRole) {
            $user->role_id = $userRole->role_id;
            $user->prodi_id = $userRole->prodi_id;
            $user->save();
        }

        return Redirect::route('letter.index');
    }
}
