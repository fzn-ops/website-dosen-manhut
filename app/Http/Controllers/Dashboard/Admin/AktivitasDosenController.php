<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Service\ActivityService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AktivitasDosenController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Menampilkan halaman daftar aktivitas dosen di Admin Panel.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/aktivitasdosen', [
            'activities' => $this->activityService->getAllActivitiesForAdmin(),
            'availableProfiles' => $this->activityService->getAvailableProfiles(),
        ]);
    }

    /**
     * Menyimpan aktivitas dosen baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'             => 'nullable|exists:users,id',
            'lecturerName'        => 'required_without:user_id|string|max:255',
            'title'               => 'required|string|max:255',
            'categories'          => 'required|array|min:1',
            'categories.*'        => 'string|max:100',
            'role'                => 'required|string|max:255',
            'description'         => 'required|string',
            'startDate'           => 'required|date',
            'endDate'             => 'nullable|date|after_or_equal:startDate',
            'lecturerQuote'       => 'nullable|string|max:1000',
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

        // Resolusi user_id dari user_id atau lecturerName
        $userId = $validated['user_id'] ?? null;
        if (!$userId && !empty($validated['lecturerName'])) {
            $user = User::where('name', $validated['lecturerName'])->first();
            $userId = $user?->id;
        }

        if (!$userId) {
            return back()->withErrors(['lecturerName' => 'Dosen yang dipilih tidak valid.']);
        }

        // Ambil file gambar yang diupload
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
            'activity_date_start' => $validated['startDate'],
            'activity_date_end'   => $validated['endDate'] ?? $validated['startDate'],
        ], $imageFiles);

        return redirect()->route('admin.aktivitasdosen')->with('success', 'Aktivitas dosen berhasil ditambahkan.');
    }

    /**
     * Memperbarui aktivitas dosen.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'             => 'nullable|exists:users,id',
            'lecturerName'        => 'required_without:user_id|string|max:255',
            'title'               => 'required|string|max:255',
            'categories'          => 'required|array|min:1',
            'categories.*'        => 'string|max:100',
            'role'                => 'required|string|max:255',
            'description'         => 'required|string',
            'startDate'           => 'required|date',
            'endDate'             => 'nullable|date|after_or_equal:startDate',
            'lecturerQuote'       => 'nullable|string|max:1000',
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

        $userId = $validated['user_id'] ?? null;
        if (!$userId && !empty($validated['lecturerName'])) {
            $user = User::where('name', $validated['lecturerName'])->first();
            $userId = $user?->id;
        }

        if (!$userId) {
            return back()->withErrors(['lecturerName' => 'Dosen yang dipilih tidak valid.']);
        }

        $imageFiles = [];
        if ($request->hasFile('images')) {
            $imageFiles = $request->file('images');
        }

        // URL gambar yang dipertahankan dari data lama
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
            'activity_date_start' => $validated['startDate'],
            'activity_date_end'   => $validated['endDate'] ?? $validated['startDate'],
        ], $imageFiles, $keptExistingImages);

        return redirect()->route('admin.aktivitasdosen')->with('success', 'Aktivitas dosen berhasil diperbarui.');
    }

    /**
     * Menghapus aktivitas dosen.
     */
    public function destroy($id): RedirectResponse
    {
        $this->activityService->deleteActivity($id);

        return redirect()->route('admin.aktivitasdosen')->with('success', 'Aktivitas dosen berhasil dihapus.');
    }
}
