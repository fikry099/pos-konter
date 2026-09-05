<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan Form Halaman Login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses Autentikasi Pengguna
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Fleksibilitas pencocokan login (menggunakan Email atau Username)
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$fieldType => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            // Skenario Guard: Tolak login jika pengguna ber-role 'karyawan' fisik
            if ($user->isKaryawan()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Akun Karyawan tidak memiliki hak akses login. Gunakan Akun Cabang untuk login POS!',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            // Pengarahan otomatis berdasarkan role
            if ($user->isOwner()) {
                return redirect()->intended(route('dashboard'));
            }

            return redirect()->intended(route('shifts.index'));
        }

        return back()->withErrors([
            'username' => 'Username/Email atau password yang Anda masukkan tidak cocok.',
        ])->onlyInput('username');
    }

    /**
     * Memproses Keluar / Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}