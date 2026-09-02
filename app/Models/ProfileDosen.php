<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProfileDosen extends Model
{
    use HasFactory;

    protected $table = 'profile_dosen';

    protected $fillable = [
        'user_id',
        'division',
        'research',
        'image',
        'educations',
        'scholar_link',
        'linkedin_link',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

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
     * Accessor untuk mendapatkan URL publik gambar profil
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!empty($this->image)) {
            return asset('storage/' . $this->image);
        }
        if ($this->relationLoaded('user') && !empty($this->user?->profile_picture)) {
            return asset('storage/' . $this->user->profile_picture);
        }
        return null;
    }

    /**
     * Relasi ke User (Dosen)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
