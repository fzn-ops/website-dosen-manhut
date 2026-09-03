<?php

namespace App\Http\Controllers\Dashboard\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Service\ActivityService;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DosenAktivitasController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Menampilkan halaman aktivitas milik dosen yang sedang login.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $user->load('profileDosen');
        $hasProfile = !empty($user->profileDosen);

        return Inertia::render('Dosen/aktivitas', [
            'activities' => $this->activityService->getActivitiesForLecturer($user->id),
            'hasProfile' => $hasProfile,
        ]);
    }

    /**
     * Menyimpan aktivitas baru khusus untuk dosen yang sedang login.
     * ID dosen diambil otomatis dari user yang sedang login.
     * Tanggal publish tidak perlu dimasukkan, diambil dari created_at.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->load('profileDosen');

        if (empty($user->profileDosen)) {
            return redirect()->back()->withErrors([
                'profile' => 'Profil publik belum dibuat oleh Administrator. Anda belum dapat mengelola aktivitas.'
            ]);
        }

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'categories'          => 'required|array|min:1',
            'categories.*'        => 'string|max:100',
            'role'                => 'required|string|max:255',
            'description'         => 'required|string',
            'startDate'           => 'required|date',
            'endDate'             => 'nullable|date|after_or_equal:startDate',
            'lecturerQuote'       => 'nullable|string|max:1000',
            'primaryImageIndex'   => 'nullable|integer|min:0|max:2',
            'images'              => 'nullable|array|max:3',
            'images.*'            => 'nullable',
        ], [
            'title.required'          => 'Judul aktivitas wajib diisi.',
            'categories.required'     => 'Pilih minimal 1 kategori aktivitas.',
            'role.required'           => 'Peran dalam kegiatan wajib diisi.',
            'description.required'    => 'Deskripsi aktivitas wajib diisi.',
            'startDate.required'      => 'Tanggal mulai kegiatan wajib diisi.',
            'endDate.after_or_equal'  => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'images.max'              => 'Maksimal 3 gambar yang dapat diunggah.',
        ]);

        $userId = $request->user()->id;

        $imageFiles = [];
        if ($request->hasFile('images')) {
            $imageFiles = $request->file('images');
        }

        $this->activityService->addActivity($userId, [
            'activity_name'       => $validated['title'],
            'description'         => $validated['description'],
            'job'                 => $validated['role'],
            'activity_type'       => $validated['categories'],
            'quote'               => $validated['lecturerQuote'] ?? null,
            'primary_image_index' => $validated['primaryImageIndex'] ?? 0,
            'activity_date_start' => $validated['startDate'],
            'activity_date_end'   => $validated['endDate'] ?? $validated['startDate'],
        ], $imageFiles);

        return redirect()->back()->with('success', 'Aktivitas berhasil ditambahkan.');
    }

    /**
     * Memperbarui aktivitas milik dosen yang sedang login.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = $request->user();
        $user->load('profileDosen');

        if (empty($user->profileDosen)) {
            return redirect()->back()->withErrors([
                'profile' => 'Profil publik belum dibuat oleh Administrator. Anda belum dapat mengelola aktivitas.'
            ]);
        }

        $userId = $user->id;

        // Pastikan aktivitas tersebut adalah milik dosen yang login
        Activity::where('id', $id)->where('user_id', $userId)->firstOrFail();

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'categories'          => 'required|array|min:1',
            'categories.*'        => 'string|max:100',
            'role'                => 'required|string|max:255',
            'description'         => 'required|string',
            'startDate'           => 'required|date',
            'endDate'             => 'nullable|date|after_or_equal:startDate',
            'lecturerQuote'       => 'nullable|string|max:1000',
            'primaryImageIndex'   => 'nullable|integer|min:0|max:2',
            'images'              => 'nullable|array|max:3',
            'images.*'            => 'nullable',
            'existingImages'      => 'nullable|array',
        ], [
            'title.required'          => 'Judul aktivitas wajib diisi.',
            'categories.required'     => 'Pilih minimal 1 kategori aktivitas.',
            'role.required'           => 'Peran dalam kegiatan wajib diisi.',
            'description.required'    => 'Deskripsi aktivitas wajib diisi.',
            'startDate.required'      => 'Tanggal mulai kegiatan wajib diisi.',
            'endDate.after_or_equal'  => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'images.max'              => 'Maksimal 3 gambar yang dapat diunggah.',
        ]);

        $imageFiles = [];
        if ($request->hasFile('images')) {
            $imageFiles = $request->file('images');
        }

        $keptExistingImages = $request->input('existingImages', []);
        if (empty($keptExistingImages) && $request->has('images')) {
            $keptExistingImages = array_filter($request->input('images', []), fn($img) => is_string($img));
        }

        $this->activityService->updateActivity($id, $userId, [
            'activity_name'       => $validated['title'],
            'description'         => $validated['description'],
            'job'                 => $validated['role'],
            'activity_type'       => $validated['categories'],
            'quote'               => $validated['lecturerQuote'] ?? null,
            'primary_image_index' => $validated['primaryImageIndex'] ?? 0,
            'activity_date_start' => $validated['startDate'],
            'activity_date_end'   => $validated['endDate'] ?? $validated['startDate'],
        ], $imageFiles, $keptExistingImages);

        return redirect()->back()->with('success', 'Aktivitas berhasil diperbarui.');
    }

    /**
     * Menghapus aktivitas milik dosen yang sedang login.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $user = $request->user();
        $user->load('profileDosen');

        if (empty($user->profileDosen)) {
            return redirect()->back()->withErrors([
                'profile' => 'Profil publik belum dibuat oleh Administrator. Anda belum dapat mengelola aktivitas.'
            ]);
        }

        $userId = $user->id;

        // Pastikan aktivitas tersebut adalah milik dosen yang login
        Activity::where('id', $id)->where('user_id', $userId)->firstOrFail();

        $this->activityService->deleteActivity($id);

        return redirect()->back()->with('success', 'Aktivitas berhasil dihapus.');
    }
}
