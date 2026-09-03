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
        return Publication::all();
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
}