<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
}
