<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    //

    protected $table = 'activity';

    protected $fillable = [
        'user_id',
        'activity_name',
        'activity_type',
        'job',
        'activity_date_start',
        'activity_date_end',
        'description',
        'quote',
    ];

    protected $casts = [
        'activity_type' => 'array',
        'activity_date_start' => 'date',
        'activity_date_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pictures()
    {
        return $this->hasMany(ActivityPicture::class, 'activity_id');
    }

    public function primaryPicture()
    {
        return $this->hasOne(ActivityPicture::class, 'activity_id')->where('is_primary', true);
    }
}
