<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPicture extends Model
{
    //
    protected $table = 'activity_pictures';

    protected $fillable = [
        'activity_id',
        'path',
        'is_primary',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function getPathAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return asset('storage/' . ltrim($value, '/'));
    }

    public function getRawPathAttribute(): ?string
    {
        return $this->getRawOriginal('path');
    }
}
