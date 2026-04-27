<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SiswaAccessWindowMiddleware
{
    private const ACCESS_END = '18:00:00';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'siswa') {
            $accessEnd = now()->copy()->setTimeFromTimeString(self::ACCESS_END);

            if (now()->gt($accessEnd)) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors([
                        'session' => 'Akses siswa ditutup setelah jam 18:00. Silakan login kembali pada jam operasional.',
                    ]);
            }
        }

        return $next($request);
    }
}
