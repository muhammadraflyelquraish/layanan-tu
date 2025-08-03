<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserLayanan;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        session(['login_source' => request('type')]);
        return view('auth.login2');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        // find user from main user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // find user from layanan
            $userLayanan = UserLayanan::query()
                ->where('email', $request->email)
                ->whereNotNull('email_verified_at')
                ->first();
            if (!$userLayanan) {
                return redirect()->route('login')->withErrors(['email' => 'Email or password invalid.'])->withInput();
            }

            if (!Hash::check($request->password, $userLayanan->password)) {
                return redirect()->route('login')->withErrors(['email' => 'Email or password invalid.'])->withInput();
            }

            $newUser = User::create([
                'name' => $userLayanan->name,
                'no_identity' => $userLayanan->nim_nip_nidn,
                'email' => $userLayanan->email,
                'password' => $userLayanan->password,
                'role_id' => 2,
                'status' => "ACTIVE",
                'user_type' => "LAYANAN",
                'email_verified' => true,
            ]);

            if (!$newUser) {
                return redirect()->route('login')->withErrors(['email' => 'Internal server error.']);
            }
        }

        $request->authenticate();

        $request->session()->regenerate();

        if (auth()->user()->status == 'INACTIVE') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive.']);
        }

        $source = session()->pull('login_source');
        if ($source == 'tracking') {
            return auth()->user()->role_id == 2
                ? redirect()->route('tracking.index')
                : redirect()->route('letter.index');
        }

        return auth()->user()->role_id == 2
            ? redirect()->route('letter.index')
            : redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(['message' => 'success'], Response::HTTP_OK);
    }
}
