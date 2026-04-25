<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect('/login');
        }

        if (\Illuminate\Support\Facades\Auth::user()->role !== $role) {
            // Izinkan admin untuk mengakses rute mahasiswa (misal untuk testing UI)
            if ($role === 'mahasiswa' && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
                return $next($request);
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
