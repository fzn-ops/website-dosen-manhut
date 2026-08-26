<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileDosen extends Model
{
    use HasFactory;

    protected $table = 'profile_dosen';

    protected $fillable = [
        'user_id',
        'division',
        'research',
        'educations',
        'scholar_link',
        'linkedin_link',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'educations' => 'array',
        ];
    }

    /**
     * Relasi ke User (Dosen)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
