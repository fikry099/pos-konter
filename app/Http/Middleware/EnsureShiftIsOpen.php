<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Shift;

class EnsureShiftIsOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil shift yang sedang berstatus 'open'
        $activeShift = Shift::getActiveShift();

        // Jika TIDAK ADA shift yang sedang aktif/open
        if (!$activeShift) {
            return redirect()->route('shifts.index')->with('warning', 'Akses Ditolak! Anda wajib membuka Shift dan mengambil foto absensi terlebih dahulu sebelum melakukan transaksi.');
        }

        // Jika ada shift aktif, izinkan akses berlanjut
        return $next($request);
    }
}