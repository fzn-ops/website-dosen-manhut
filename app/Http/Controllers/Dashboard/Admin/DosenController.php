<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DosenController extends Controller
{
    /**
     * Menampilkan daftar akun dosen di Admin Panel (Inertia Vue).
     */
    public function index(): Response
    {
        $lecturers = User::where('role', 'dosen')
            ->latest()
            ->get()
            ->map(function ($dosen) {
                return [
                    'id' => $dosen->id,
                    'name' => $dosen->name,
                    'nip' => $dosen->NIP ?? '-',
                    'email' => $dosen->email ?? '-',
                    'username' => $dosen->username ?? '',
                    'phone' => $dosen->phone ?? '-',
                    'profile_picture' => $dosen->profile_picture,
                ];
            });

        return Inertia::render('Admin/dosen', [
            'lecturers' => $lecturers,
        ]);
    }

    /**
     * Menyimpan data akun dosen baru (role: dosen).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:users,NIP',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama dosen wajib diisi.',
            'nip.required' => 'NIP dosen wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $username = (!empty($validated['username']) && $validated['username'] !== '-')
            ? Str::slug($validated['username'], '')
            : null;

        $email = (!empty($validated['email']) && $validated['email'] !== '-')
            ? $validated['email']
            : null;

        $phone = (!empty($validated['phone']) && $validated['phone'] !== '-')
            ? $validated['phone']
            : null;

        $password = !empty($validated['password']) ? $validated['password'] : $validated['nip'];

        User::create([
            'name' => $validated['name'],
            'NIP' => $validated['nip'],
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'dosen',
        ]);

        return redirect()->route('admin.dosen')->with('success', 'Akun dosen berhasil ditambahkan.');
    }

    /**
     * Memperbarui data akun dosen.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::where('role', 'dosen')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:users,NIP,' . $id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama dosen wajib diisi.',
            'nip.required' => 'NIP dosen wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'NIP' => $validated['nip'],
            'username' => (!empty($validated['username']) && $validated['username'] !== '-') ? Str::slug($validated['username'], '') : null,
            'email' => (!empty($validated['email']) && $validated['email'] !== '-') ? $validated['email'] : null,
            'phone' => (!empty($validated['phone']) && $validated['phone'] !== '-') ? $validated['phone'] : null,
        ];

        if (!empty($validated['password']) && trim($validated['password']) !== '') {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

        return redirect()->route('admin.dosen')->with('success', 'Akun dosen berhasil diperbarui.');
    }

    /**
     * Menghapus akun dosen.
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::where('role', 'dosen')->findOrFail($id);
        $user->delete();

        return redirect()->route('admin.dosen')->with('success', 'Akun dosen berhasil dihapus.');
    }

    /**
     * Import batch data akun dosen dari file Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'lecturers' => 'required|array|min:1',
            'lecturers.*.name' => 'required|string|max:255',
            'lecturers.*.nip' => 'required|string|max:50',
            'lecturers.*.username' => 'nullable|string|max:255',
            'lecturers.*.email' => 'nullable|email|max:255',
            'lecturers.*.phone' => 'nullable|string',
            'lecturers.*.password' => 'nullable|string',
        ]);

        $insertedCount = 0;
        foreach ($request->input('lecturers') as $item) {
            $nip = trim($item['nip'] ?? '');
            $name = trim($item['name'] ?? '');
            $rawUsername = trim($item['username'] ?? '');
            $rawEmail = trim($item['email'] ?? '');
            $rawPhone = trim($item['phone'] ?? '');
            $rawPassword = trim($item['password'] ?? '');

            if (!$nip || !$name || $nip === '-' || $name === 'Tanpa Nama') {
                continue;
            }

            // Cek apakah NIP sudah ada
            if (User::where('NIP', $nip)->exists()) {
                continue;
            }

            $username = (!empty($rawUsername) && $rawUsername !== '-') ? Str::slug($rawUsername, '') : null;
            if ($username && User::where('username', $username)->exists()) {
                $username = null;
            }

            $email = (!empty($rawEmail) && $rawEmail !== '-') ? $rawEmail : null;
            if ($email && User::where('email', $email)->exists()) {
                $email = null;
            }

            $phone = (!empty($rawPhone) && $rawPhone !== '-') ? $rawPhone : null;
            $password = (!empty($rawPassword) && $rawPassword !== '-') ? $rawPassword : $nip;

            User::create([
                'name' => $name,
                'NIP' => $nip,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role' => 'dosen',
            ]);

            $insertedCount++;
        }

        return redirect()->route('admin.dosen')->with('success', "Berhasil mengimpor {$insertedCount} data akun dosen.");
    }
}
