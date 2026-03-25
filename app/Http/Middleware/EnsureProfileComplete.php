<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                return $next($request);
            }

            $profile = $user->profile;

            if (!$profile || !$profile->isComplete()) {
                if ($request->routeIs('profile.*') || $request->routeIs('logout')) {
                    return $next($request);
                }

                return redirect()->route('profile.edit')
                    ->with('warning', 'Lengkapi profil terlebih dahulu untuk mulai belajar.');
            }
        }

        return $next($request);
    }
}