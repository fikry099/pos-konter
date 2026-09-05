<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user login dan memiliki role owner
        if (auth()->check() && auth()->user()->isOwner()) {
            return $next($request);
        }

        // Jika karyawan mencoba mengakses halaman khusus owner
        return redirect()->route('pos.index')->with('error', 'Akses ditolak! Halaman ini hanya untuk Owner.');
    }
}