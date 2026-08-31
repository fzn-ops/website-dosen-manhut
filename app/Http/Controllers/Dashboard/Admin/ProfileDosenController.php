<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Service\DosenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileDosenController extends Controller
{
    protected DosenService $dosenService;

    public function __construct(DosenService $dosenService)
    {
        $this->dosenService = $dosenService;
    }

    /**
     * Menampilkan daftar profil dosen di Admin Panel (Inertia Vue).
     */
    public function index(): Response
    {
        return Inertia::render('Admin/profiledosen', [
            'profiles' => $this->dosenService->getProfilesForInertia(),
            'availableLecturers' => $this->dosenService->getAvailableLecturers(),
        ]);
    }

    /**
     * Menyimpan profil dosen baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:profile_dosen,user_id',
            'division' => 'required|string|max:255',
            'research' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'educations' => 'nullable|array',
            'educations.*.degree' => 'nullable|string|max:50',
            'educations.*.university' => 'nullable|string|max:255',
            'educations.*.major' => 'nullable|string|max:255',
            'educations.*.graduationYear' => 'nullable|string|max:10',
            'scholar_link' => 'nullable|string|max:255',
            'linkedin_link' => 'nullable|string|max:255',
        ], [
            'user_id.required' => 'Nama dosen wajib dipilih.',
            'user_id.exists' => 'Akun dosen yang dipilih tidak terdaftar di sistem.',
            'user_id.unique' => 'Dosen ini sudah memiliki data profil.',
            'division.required' => 'Divisi dosen wajib dipilih.',
            'image.image' => 'File foto harus berupa format gambar yang valid (JPG, PNG, JPEG).',
            'image.max' => 'Ukuran foto profil maksimal 10MB.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('profile_pictures', 'public');
        }

        $this->dosenService->createProfile($validated);

        return redirect()->back()->with('success', 'Profil dosen berhasil ditambahkan.');
    }

    /**
     * Memperbarui profil dosen.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'division' => 'required|string|max:255',
            'research' => 'nullable|string',
            'image' => 'nullable',
            'educations' => 'nullable|array',
            'educations.*.degree' => 'nullable|string|max:50',
            'educations.*.university' => 'nullable|string|max:255',
            'educations.*.major' => 'nullable|string|max:255',
            'educations.*.graduationYear' => 'nullable|string|max:10',
            'scholar_link' => 'nullable|string|max:255',
            'linkedin_link' => 'nullable|string|max:255',
        ], [
            'division.required' => 'Divisi dosen wajib dipilih.',
        ]);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg|max:10240',
            ], [
                'image.image' => 'File foto harus berupa format gambar yang valid (JPG, PNG, JPEG).',
                'image.max' => 'Ukuran foto profil maksimal 10MB.',
            ]);
            $validated['image'] = $request->file('image')->store('profile_pictures', 'public');
        } elseif ($request->input('image') === null || $request->input('image') === '') {
            $validated['image'] = null;
        } else {
            // String URL / path lama yang tidak diubah
            unset($validated['image']);
        }

        $this->dosenService->updateProfile($id, $validated);

        return redirect()->back()->with('success', 'Profil dosen berhasil diperbarui.');
    }

    /**
     * Menghapus profil dosen.
     */
    public function destroy($id): RedirectResponse
    {
        $this->dosenService->deleteProfile($id);

        return redirect()->back()->with('success', 'Profil dosen berhasil dihapus.');
    }
}
