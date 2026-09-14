<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'artist',
        'featured_artists',
        'genre',
        'release_date',
        'cover_path',
        'audio_path',
        'status',
        'isrc',
        'upc',
        'project_id',
        'track_number',
        'credits',
    ];

    protected $casts = [
        'release_date' => 'date',
        'user_id'      => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function stats()
    {
        return $this->hasMany(Stat::class);
    }
    public function project() { 
        return $this->belongsTo(Project::class); 
        
    }
    public function copyrightScan()
    {
        return $this->hasOne(CopyrightScan::class, 'audio_path', 'audio_path');
    }

    public function artistModel()
    {
        return $this->hasOneThrough(Artist::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }
    
  public function getCoverUrlAttribute()
{
    if (!$this->cover_path) {
        return null;
    }

    return assetPath($this->cover_path);
}
public function getArtistDisplayAttribute()
{
    $main = $this->artist ?? null;
    $featured = $this->featured_artists ?? null;

    if ($main && $featured) {
        return $main . ' feat. ' . $featured;
    }

    return $main ?: $featured ?: 'Unknown Artist';
}


}
