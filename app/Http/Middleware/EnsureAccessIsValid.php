<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnsureAccessIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    private $patternUri = [
        '/dashboard'     => 'DASHBOARD',
        '/role'          => 'ROLE',
        '/user'          => 'USER',
        '/letter'        => 'LETTER',
        '/spj'           => 'SPJ',
        '/label-spj'     => 'LABEL_SPJ',
        '/disposisi'     => 'DISPOSISI',
        '/arsip'         => 'ARSIP',
    ];

    public function handle(Request $request, Closure $next)
    {
        $uri = $request->path();

        $matchedKey = null;
        foreach ($this->patternUri as $prefix => $value) {
            if (Str::startsWith('/' . $uri, $prefix)) {
                $matchedKey = $value;
                break;
            }
        }

        // Skip validate is not match key
        if ($matchedKey == null) {
            return $next($request);
        }

        // Check permission
        foreach ($request->user()->role->permissions as $permission) {
            if ($permission->menu == $matchedKey) {
                if ($permission->is_permitted) {
                    return $next($request);
                } else {
                    abort(403, 'Unauthorized action.');
                }
            } else {
                continue;
            }
        }

        return $next($request);
    }
}
