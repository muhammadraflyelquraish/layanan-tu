<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    function redirectToGoogle(Request $request)
    {
        return Socialite::driver('google')->redirect();
    }

    function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'email_verified' => true,
                    'avatar' => $googleUser->getAvatar(),
                    'role_id' => 2,
                    'password' => null,
                    'user_type' => "GOOGLE",
                    'status' => 'ACTIVE'
                ]
            );

            if ($user->status == 'INACTIVE') {
                return redirect()->route('login')->withErrors(['email' => 'Your account is currently inactive.']);
            }

            Auth::login($user, true);

            return redirect()->route('letter.index');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('email', 'Login google failed');
        }
    }
}
