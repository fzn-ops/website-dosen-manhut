<?php

namespace App\Http\Service;

use App\Models\Activity;
use App\Models\ActivityPicture;
use App\Models\ProfileDosen;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class ActivityService
{
    /**
     * Mengambil daftar dosen yang memiliki profil di tabel profile_dosen
     * untuk dropdown pemilihan nama dosen di form aktivitas.
     */
    public function getAvailableProfiles(): array
    {
        return ProfileDosen::with('user')
            ->whereHas('user')
            ->get()
            ->map(function ($profile) {
                return [
                    'id' => $profile->id,
                    'user_id' => $profile->user_id,
                    'name' => $profile->user->name,
                    'nip' => $profile->user->NIP ?? '-',
                    'division' => $profile->division ?? '-',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Mengambil semua data aktivitas untuk Admin Panel (Inertia Vue).
     */
    public function getAllActivitiesForAdmin(): array
    {
        Carbon::setLocale('id');

        return Activity::with(['user.profileDosen', 'pictures'])
            ->latest('id')
            ->get()
            ->map(function ($act) {
                $pictures = $act->pictures;
                $images = $pictures->map(fn($p) => $p->path)->values()->all();

                $primaryPic = $act->primaryPicture ?? $pictures->first();
                $primaryIndex = 0;
                if ($primaryPic) {
                    $foundIdx = $pictures->search(fn($p) => $p->id === $primaryPic->id);
                    if ($foundIdx !== false) {
                        $primaryIndex = $foundIdx;
                    }
                }

                $publishDate = $act->created_at
                    ? Carbon::parse($act->created_at)->translatedFormat('d F Y')
                    : ($act->activity_date_start ? Carbon::parse($act->activity_date_start)->translatedFormat('d F Y') : '-');

                $dateSort = $act->created_at
                    ? Carbon::parse($act->created_at)->format('Y-m-d H:i:s')
                    : ($act->activity_date_start ? Carbon::parse($act->activity_date_start)->format('Y-m-d') : '');

                $formattedEventDate = '-';
                if ($act->activity_date_start) {
                    $startStr = Carbon::parse($act->activity_date_start)->translatedFormat('d F Y');
                    if ($act->activity_date_end && $act->activity_date_end != $act->activity_date_start) {
                        $endStr = Carbon::parse($act->activity_date_end)->translatedFormat('d F Y');
                        $formattedEventDate = "{$startStr} - {$endStr}";
                    } else {
                        $formattedEventDate = $startStr;
                    }
                }

                $categories = is_array($act->activity_type)
                    ? $act->activity_type
                    : (json_decode($act->activity_type, true) ?? []);

                return [
                    'id' => $act->id,
                    'user_id' => $act->user_id,
                    'name' => $act->activity_name,
                    'title' => $act->activity_name,
                    'lecturer' => $act->user?->name ?? 'Tanpa Nama',
                    'lecturerName' => $act->user?->name ?? 'Tanpa Nama',
                    'description' => $act->description ?? '',
                    'role' => $act->job ?? '',
                    'startDate' => $act->activity_date_start ? Carbon::parse($act->activity_date_start)->format('Y-m-d') : '',
                    'endDate' => $act->activity_date_end ? Carbon::parse($act->activity_date_end)->format('Y-m-d') : '',
                    'categories' => $categories,
                    'category' => count($categories) ? $categories[0] : 'Lainnya',
                    'publishDate' => $publishDate,
                    'date' => $publishDate,
                    'eventDate' => $formattedEventDate,
                    'dateSort' => $dateSort,
                    'images' => $images,
                    'imagePreviews' => $images,
                    'primaryImageIndex' => $primaryIndex,
                    'lecturerQuote' => $act->quote && $act->quote !== '-' ? $act->quote : '-',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Mengambil detail aktivitas berdasarkan ID.
     */
    public function getActivityById($activityId)
    {
        return Activity::with(['user', 'pictures'])->findOrFail($activityId);
    }

    /**
     * Mengambil data aktivitas terpaginasi untuk Landing Page.
     */
    public function getAllActivitiesPaginated($keyword = null, $perPage = 9)
    {
        $query = Activity::with(['user', 'primaryPicture', 'pictures']);

        if ($keyword) {
            $query->where('activity_name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
        }

        return $query->orderBy('activity_date_start', 'desc')
                     ->paginate($perPage);
    }

    /**
     * Mengambil data aktivitas acak untuk rekomendasi.
     */
    public function getRandomActivities($excludeActivityId, $limit = 3)
    {
        return Activity::with(['user', 'primaryPicture', 'pictures'])
            ->where('id', '!=', $excludeActivityId)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Mengambil aktivitas berdasarkan User ID terpaginasi.
     */
    public function getActivitiesByUserIdPaginated($userId, $searchKeyword = null, $perPage = 5)
    {
        $query = Activity::with('pictures')
                         ->where('user_id', $userId);

        if ($searchKeyword) {
            $query->where(function($q) use ($searchKeyword) {
                $q->where('activity_name', 'like', "%{$searchKeyword}%")
                  ->orWhere('description', 'like', "%{$searchKeyword}%");
            });
        }

        return $query->orderBy('activity_date_start', 'desc')
                     ->paginate($perPage);
    }

    /**
     * Menambahkan aktivitas baru beserta gambar (maksimal 3 gambar di tabel activity_pictures).
     */
    public function addActivity($userId, array $data, $imageFiles = [])
    {
        $activity = Activity::create([
            'user_id'             => $userId,
            'activity_name'       => $data['activity_name'] ?? $data['name'] ?? $data['title'],
            'description'         => $data['description'] ?? null,
            'job'                 => $data['job'] ?? $data['role'] ?? null,
            'activity_type'       => $data['activity_type'] ?? $data['categories'] ?? [],
            'quote'               => (!empty($data['quote']) && $data['quote'] !== '-') ? $data['quote'] : null,
            'activity_date_start' => $data['activity_date_start'] ?? $data['startDate'] ?? null,
            'activity_date_end'   => $data['activity_date_end'] ?? $data['endDate'] ?? ($data['activity_date_start'] ?? $data['startDate'] ?? null),
        ]);

        if (!empty($imageFiles) && is_array($imageFiles)) {
            $filesToUpload = array_slice($imageFiles, 0, 3);
            $primaryIndex = isset($data['primary_image_index']) ? (int)$data['primary_image_index'] : 0;
            $primaryIndex = max(0, min($primaryIndex, count($filesToUpload) - 1));

            foreach ($filesToUpload as $idx => $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $path = $file->store('activities', 'public');

                    ActivityPicture::create([
                        'activity_id' => $activity->id,
                        'path'        => $path,
                        'is_primary'  => ($idx === $primaryIndex),
                    ]);
                }
            }
        }

        return $activity;
    }

    /**
     * Memperbarui data aktivitas dan gambar (maksimal 3 gambar).
     */
    public function updateActivity($id, $userId, array $data, $newImageFiles = [], $keptExistingImages = [])
    {
        $activity = Activity::with('pictures')->findOrFail($id);

        $activity->update([
            'user_id'             => $userId,
            'activity_name'       => $data['activity_name'] ?? $data['name'] ?? $data['title'],
            'description'         => $data['description'] ?? null,
            'job'                 => $data['job'] ?? $data['role'] ?? null,
            'activity_type'       => $data['activity_type'] ?? $data['categories'] ?? [],
            'quote'               => (!empty($data['quote']) && $data['quote'] !== '-') ? $data['quote'] : null,
            'activity_date_start' => $data['activity_date_start'] ?? $data['startDate'] ?? null,
            'activity_date_end'   => $data['activity_date_end'] ?? $data['endDate'] ?? ($data['activity_date_start'] ?? $data['startDate'] ?? null),
        ]);

        // Hapus gambar yang tidak dipertahankan oleh user
        $keptUrls = array_map(function ($img) {
            return is_string($img) ? basename(parse_url($img, PHP_URL_PATH)) : '';
        }, is_array($keptExistingImages) ? $keptExistingImages : []);

        foreach ($activity->pictures as $existingPic) {
            $rawPath = $existingPic->getRawOriginal('path');
            $fileName = basename($rawPath);

            if (!in_array($fileName, $keptUrls)) {
                if (Storage::disk('public')->exists($rawPath)) {
                    Storage::disk('public')->delete($rawPath);
                }
                $existingPic->delete();
            }
        }

        // Upload gambar baru jika masih ada slot (maksimal total 3 gambar)
        $currentCount = $activity->pictures()->count();
        $remainingSlots = max(0, 3 - $currentCount);

        if (!empty($newImageFiles) && is_array($newImageFiles) && $remainingSlots > 0) {
            $filesToUpload = array_slice($newImageFiles, 0, $remainingSlots);

            foreach ($filesToUpload as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $path = $file->store('activities', 'public');

                    ActivityPicture::create([
                        'activity_id' => $activity->id,
                        'path'        => $path,
                        'is_primary'  => false,
                    ]);
                }
            }
        }

        // Set primary picture sesuai pilihan user
        $allPictures = $activity->pictures()->get();
        if ($allPictures->isNotEmpty()) {
            $primaryIndex = isset($data['primary_image_index']) ? (int)$data['primary_image_index'] : 0;
            $primaryIndex = max(0, min($primaryIndex, $allPictures->count() - 1));

            ActivityPicture::where('activity_id', $activity->id)->update(['is_primary' => false]);

            if (isset($allPictures[$primaryIndex])) {
                $allPictures[$primaryIndex]->update(['is_primary' => true]);
            } else {
                $allPictures->first()->update(['is_primary' => true]);
            }
        }

        return $activity;
    }

    /**
     * Menghapus data aktivitas beserta file gambarnya dari storage.
     */
    public function deleteActivity($id): void
    {
        $activity = Activity::with('pictures')->findOrFail($id);

        foreach ($activity->pictures as $pic) {
            $rawPath = $pic->getRawOriginal('path');
            if ($rawPath && Storage::disk('public')->exists($rawPath)) {
                Storage::disk('public')->delete($rawPath);
            }
        }

        $activity->delete();
    }
}