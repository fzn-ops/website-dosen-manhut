<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class EnsureDosenPasswordChanged
{
    /**
     * Handle an incoming request.
     * Mencegah dosen mengakses dashboard & aktivitas jika masih menggunakan password default (NIP).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'dosen') {
            $sessionKey = 'auth_is_default_password_' . $user->id;
            if ($request->session()->has($sessionKey)) {
                $isDefaultPassword = $request->session()->get($sessionKey);
            } else {
                $isDefaultPassword = !empty($user->NIP) && Hash::check($user->NIP, $user->password);
                $request->session()->put($sessionKey, $isDefaultPassword);
            }

            $isEmailEmpty = empty($user->email) || $user->email === '-';
            $isLocked = $isDefaultPassword || $isEmailEmpty;

            if ($isLocked) {
                // Rute yang tetap boleh diakses saat akun belum lengkap/terkunci
                $allowedRouteNames = [
                    'dosen.profile',
                    'dosen.profile.personal',
                    'dosen.profile.account',
                    'dosen.profile.password',
                    'logout',
                ];

                $currentRouteName = $request->route()?->getName();

                if (!in_array($currentRouteName, $allowedRouteNames)) {
                    if ($isDefaultPassword && $isEmailEmpty) {
                        $msg = 'Akun Anda belum lengkap. Silakan lengkapi email aktif dan ganti password default (NIP) Anda untuk membuka akses Dashboard dan Aktivitas.';
                    } elseif ($isEmailEmpty) {
                        $msg = 'Email akun Anda masih kosong. Silakan lengkapi email aktif Anda terlebih dahulu untuk membuka akses Dashboard dan Aktivitas.';
                    } else {
                        $msg = 'Akun Anda masih menggunakan password default (NIP). Silakan ubah password Anda terlebih dahulu untuk membuka akses Dashboard dan Aktivitas.';
                    }

                    return redirect()->route('dosen.profile')->with('warning', $msg);
                }
            }
        }

        return $next($request);
    }
}
