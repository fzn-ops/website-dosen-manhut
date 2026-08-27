<?php

namespace App\Http\Service;

use App\Models\ProfileDosen;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class DosenService
{
    /**
     * Mengambil semua profil dosen beserta relasi akun User.
     * Mendukung filter pencarian (search) dan filter divisi (division).
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllProfiles(array $filters = []): Collection
    {
        $query = ProfileDosen::with('user')->latest();

        // Filter berdasarkan Divisi jika ada
        if (!empty($filters['division'])) {
            $query->where('division', $filters['division']);
        }

        // Filter berdasarkan Pencarian (Nama Dosen atau NIP)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('NIP', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Format data profil dosen khusus untuk dikirim ke Inertia Vue (Admin Panel).
     * Menghasilkan struktur array yang siap pakai di tabel profiledosen.vue.
     *
     * @param Collection|null $profiles
     * @return array
     */
    public function getProfilesForInertia(?Collection $profiles = null): array
    {
        $profiles = $profiles ?? $this->getAllProfiles();

        return $profiles->map(function ($profile) {
            $educationSummary = '-';
            if (!empty($profile->educations) && is_array($profile->educations)) {
                $eduItems = array_map(function ($edu) {
                    $degree = !empty($edu['degree']) ? "{$edu['degree']}: " : '';
                    $major = $edu['major'] ?? '';
                    $univ = $edu['university'] ?? '';
                    $year = !empty($edu['graduationYear']) ? " ({$edu['graduationYear']})" : '';
                    return trim("{$degree}{$major} - {$univ}{$year}", " -");
                }, $profile->educations);
                $educationSummary = implode('; ', array_filter($eduItems));
            }

            $imageUrl = $profile->image_url;

            return [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'name' => $profile->user->name ?? '-',
                'nip' => $profile->user->NIP ?? '-',
                'division' => $profile->division,
                'educations' => $profile->educations ?? [],
                'educationSummary' => $educationSummary,
                'research' => $profile->research ?? '-',
                'contact' => $profile->user->email ?? '-',
                'scholarLink' => $profile->scholar_link ?? '',
                'linkedinLink' => $profile->linkedin_link ?? '',
                'scholar_link' => $profile->scholar_link ?? '',
                'linkedin_link' => $profile->linkedin_link ?? '',
                'image' => $imageUrl,
                'imagePreview' => $imageUrl,
                'profile_picture' => $imageUrl ?? $profile->user->profile_picture ?? null,
            ];
        })->toArray();
    }

    /**
     * Format data profil dosen khusus untuk Blade View (Landing Page / Public Lecturers).
     * Menyediakan slug URL ramah SEO, kategori CSS, dan URL foto profil.
     *
     * @param Collection|null $profiles
     * @return array
     */
    public function getProfilesForBlade(?Collection $profiles = null): array
    {
        $profiles = $profiles ?? $this->getAllProfiles();

        return $profiles->map(function ($profile) {
            $name = $profile->user->name ?? 'Dosen';
            $slug = $profile->user->username ?? Str::slug($name);

            // Kategori ramah filter frontend blade
            $categorySlug = match (strtolower(trim($profile->division))) {
                'perencanaan kehutanan' => 'perencanaan',
                'pemanfaatan sumberdaya hutan' => 'pemanfaatan',
                'kebijakan kehutanan' => 'kebijakan',
                default => 'umum',
            };

            $imageUrl = $profile->image_url ?? $profile->user->profile_picture ?? '/assets/images/default-avatar.png';

            return [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'nama' => $name,
                'name' => $name,
                'nip' => $profile->user->NIP ?? '-',
                'divisi' => $profile->division,
                'division' => $profile->division,
                'kategori' => $categorySlug,
                'slug' => $slug,
                'educations' => $profile->educations ?? [],
                'research' => $profile->research ?? '-',
                'email' => $profile->user->email ?? '-',
                'scholar_link' => $profile->scholar_link ?? '',
                'linkedin_link' => $profile->linkedin_link ?? '',
                'foto' => $imageUrl,
                'image' => $imageUrl,
            ];
        })->toArray();
    }

    /**
     * Mengambil daftar dosen yang berstatus User role 'dosen'.
     * Digunakan untuk dropdown pilihan akun dosen pada form modal.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableLecturers(): \Illuminate\Support\Collection
    {
        return User::where('role', 'dosen')
            ->select('id', 'name', 'NIP as nip', 'email', 'username')
            ->get();
    }

    /**
     * Mencari profil dosen berdasarkan ID.
     *
     * @param int|string $id
     * @return ProfileDosen
     */
    public function getProfileById($id): ProfileDosen
    {
        return ProfileDosen::with('user')->findOrFail($id);
    }

    /**
     * Mencari profil dosen berdasarkan slug atau username (untuk halaman publik Blade: /dosen/{slug}).
     *
     * @param string $slug
     * @return ProfileDosen|null
     */
    public function getProfileBySlug(string $slug): ?ProfileDosen
    {
        return ProfileDosen::with('user')
            ->whereHas('user', function ($q) use ($slug) {
                $q->where('username', $slug)
                  ->orWhere('name', 'like', "%" . str_replace('-', ' ', $slug) . "%");
            })
            ->first();
    }

    /**
     * Menyimpan profil dosen baru.
     *
     * @param array $data
     * @return ProfileDosen
     */
    public function createProfile(array $data): ProfileDosen
    {
        return ProfileDosen::create($data);
    }

    /**
     * Memperbarui profil dosen yang sudah ada.
     *
     * @param int|string $id
     * @param array $data
     * @return ProfileDosen
     */
    public function updateProfile($id, array $data): ProfileDosen
    {
        $profile = $this->getProfileById($id);

        if (isset($data['image']) && $profile->image && $profile->image !== $data['image']) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($profile->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->image);
            }
        }

        $profile->update($data);

        return $profile;
    }

    /**
     * Menghapus profil dosen berdasarkan ID.
     *
     * @param int|string $id
     * @return bool|null
     */
    public function deleteProfile($id): ?bool
    {
        $profile = $this->getProfileById($id);

        if ($profile->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->image);
        }

        return $profile->delete();
    }
}
