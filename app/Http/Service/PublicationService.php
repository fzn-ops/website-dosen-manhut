<?php

namespace App\Http\Service;

use App\Models\ProfileDosen;
use App\Models\User;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PublicationService
{
    public function getPublicationsByUserId(int $userId): Collection
    {
        return Publication::where('user_id', $userId)->get();
    }

    public function getAllPublications(): Collection
    {
        return Publication::with('user:id,name,email')->get();
    }

    public function destroyPublication(int $publicationId): bool
    {
        $publication = Publication::find($publicationId);
        if ($publication) {
            return $publication->delete();
        }
        return false;
    }

    public function destroyAllPublications(): bool
    {
        try {
            Publication::query()->delete();
            return true;
        } catch (\Exception $e){
            // Log error jika ingin melihat alasannya di console
            \Illuminate\Support\Facades\Log::error('Gagal hapus semua: ' . $e->getMessage());
            return false;
        }
    }

    public function deletePublicationsBatch(string $type = 'all', array $dosenIds = [], array $years = []): int
    {
        try {
            if ($type === 'lecturers' && !empty($dosenIds)) {
                $cleanIds = array_filter(array_map('intval', $dosenIds));
                return Publication::whereIn('user_id', $cleanIds)->delete();
            }

            if ($type === 'years' && !empty($years)) {
                $hasNoYear = in_array('Tanpa Tahun', $years, true) || in_array(null, $years, true) || in_array('', $years, true);
                $validNumericYears = array_values(array_filter($years, fn($y) => is_numeric($y)));

                return Publication::where(function ($query) use ($validNumericYears, $hasNoYear) {
                    if (!empty($validNumericYears)) {
                        $query->whereIn('year', $validNumericYears);
                    }
                    if ($hasNoYear) {
                        if (!empty($validNumericYears)) {
                            $query->orWhereNull('year')->orWhere('year', '');
                        } else {
                            $query->whereNull('year')->orWhere('year', '');
                        }
                    }
                })->delete();
            }

            // Default: delete all
            return Publication::query()->delete();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal hapus batch publikasi: ' . $e->getMessage());
            return -1;
        }
    }
}