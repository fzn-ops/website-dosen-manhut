<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    //
    protected $fillable = [
        'user_id',
        'title',
        'authors',
        'publisher',
        'cited_by',
        'year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
