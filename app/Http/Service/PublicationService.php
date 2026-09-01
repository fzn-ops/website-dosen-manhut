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
}