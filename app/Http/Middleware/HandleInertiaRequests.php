<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $userData = null;

        if ($user) {
            $isDefaultPassword = false;
            $profilePic = null;

            if ($user->role === 'dosen') {
                $pwdSessionKey = 'auth_is_default_password_' . $user->id;
                if ($request->session()->has($pwdSessionKey)) {
                    $isDefaultPassword = $request->session()->get($pwdSessionKey);
                } else {
                    $isDefaultPassword = !empty($user->NIP) && Hash::check($user->NIP, $user->password);
                    $request->session()->put($pwdSessionKey, $isDefaultPassword);
                }

                $user->load('profileDosen');
                $profileDosen = $user->profileDosen;
                $profilePic = $profileDosen?->image_url
                    ?? ($profileDosen?->image ? asset('storage/' . $profileDosen->image) : null)
                    ?? ($user->profile_picture ? asset('storage/' . $user->profile_picture) : null);

                if ($request->session()->has('auth_profile_picture_' . $user->id)) {
                    $request->session()->forget('auth_profile_picture_' . $user->id);
                }
            } else {
                $profilePic = $user->profile_picture ? asset('storage/' . $user->profile_picture) : null;
            }

            $isEmailEmpty = empty($user->email) || $user->email === '-';
            $isLocked = $user->role === 'dosen' ? ($isDefaultPassword || $isEmailEmpty) : false;

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'nip' => $user->NIP,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_picture' => $profilePic,
                'is_default_password' => $isDefaultPassword,
                'is_email_empty' => $isEmailEmpty,
                'is_locked' => $isLocked,
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $userData,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
        ];
    }
}
