<?php

namespace App\Http\Controllers\Dashboard\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DosenProfileController extends Controller
{
    /**
     * Menampilkan halaman Profile Dosen.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $user->load('profileDosen');
        $profileDosen = $user->profileDosen;
        $photoUrl = $profileDosen?->image_url ?? ($profileDosen?->image ? asset('storage/' . $profileDosen->image) : null);
        $hasProfileDosen = !empty($profileDosen);
        $hasPhoto = !empty($photoUrl);

        $pwdSessionKey = 'auth_is_default_password_' . $user->id;
        if ($request->session()->has($pwdSessionKey)) {
            $isDefaultPassword = $request->session()->get($pwdSessionKey);
        } else {
            $isDefaultPassword = !empty($user->NIP) && Hash::check($user->NIP, $user->password);
            $request->session()->put($pwdSessionKey, $isDefaultPassword);
        }
        $isEmailEmpty = empty($user->email) || $user->email === '-';
        $isLocked = $isDefaultPassword || $isEmailEmpty;

        return Inertia::render('Dosen/profile', [
            'userData' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->NIP ?? '-',
                'username' => $user->username && $user->username !== '-' ? $user->username : '',
                'email' => $user->email && $user->email !== '-' ? $user->email : '',
                'phone' => $user->phone && $user->phone !== '-' ? $user->phone : '',
                'profile_picture' => $photoUrl,
            ],
            'isDefaultPassword' => $isDefaultPassword,
            'isEmailEmpty' => $isEmailEmpty,
            'isLocked' => $isLocked,
            'hasProfileDosen' => $hasProfileDosen,
            'hasPhoto' => $hasPhoto,
        ]);
    }

    /**
     * Memperbarui foto diri dosen.
     */
    public function updatePersonal(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            ], [
                'photo.image' => 'File harus berupa gambar.',
                'photo.mimes' => 'Format foto harus JPEG, JPG, atau PNG.',
                'photo.max' => 'Ukuran foto maksimal 10MB.',
            ]);

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('photo')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
            $user->save();
        } elseif ($request->boolean('remove_photo')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = null;
            $user->save();
        }

        return back()->with('success', 'Data diri berhasil diperbarui.');
    }

    /**
     * Memperbarui data akun dosen (username, email dan nomor handphone).
     */
    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->username = !empty($validated['username']) ? $validated['username'] : null;
        $user->email = $validated['email'];
        $user->phone = !empty($validated['phone']) ? $validated['phone'] : null;
        $user->save();

        $isDefaultPassword = !empty($user->NIP) && Hash::check($user->NIP, $user->password);
        if ($isDefaultPassword) {
            $msg = 'Email berhasil disimpan! Silakan ubah password default (NIP) Anda untuk membuka akses Dashboard dan Aktivitas.';
        } else {
            $msg = 'Email berhasil disimpan! Akses Dashboard dan Aktivitas kini telah terbuka.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Mengganti password akun dosen.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Cegah penggunaan NIP sebagai password baru
        if (!empty($user->NIP) && $validated['new_password'] === $user->NIP) {
            return back()->withErrors([
                'new_password' => 'Password baru tidak boleh sama dengan password default (NIP).',
            ]);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // Update session cache menjadi false secara instan
        $request->session()->put('auth_is_default_password_' . $user->id, false);

        $isEmailEmpty = empty($user->email) || $user->email === '-';
        if ($isEmailEmpty) {
            $msg = 'Password berhasil diubah! Silakan lengkapi email aktif Anda untuk membuka akses Dashboard dan Aktivitas.';
        } else {
            $msg = 'Password berhasil diubah! Akses Dashboard dan Aktivitas kini telah terbuka penuh.';
        }

        return back()->with('success', $msg);
    }
}
