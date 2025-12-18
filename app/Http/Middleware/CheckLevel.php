<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLevel
{
    public function handle(Request $request, Closure $next, ...$levels)
    {
        $user = auth()->user();

        // kalau belum login (jaga-jaga)
        if (!$user) {
            return redirect()->route('login');
        }

        // OWNER (0) → bebas akses semua
        if ($user->level == 0) {
            return $next($request);
        }

        // selain owner → cek level
        if (!in_array((string)$user->level, $levels, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
