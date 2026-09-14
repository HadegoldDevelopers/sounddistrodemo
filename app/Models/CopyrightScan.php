<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CopyrightScan extends Model
{
    protected $fillable = [
        'user_id',
        'audio_path',
        'status',
        'matched_title',
        'matched_artist',
        'response',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}