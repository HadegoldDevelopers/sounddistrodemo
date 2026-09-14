<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'type',
        'cover_path',
        'release_date',
        'genre',
        'subgenre',
        'language',
        'explicit',
        'label',
        'songwriter',
        'upc',
    ];

    protected $casts = [
        'user_id'      => 'integer',
        'release_date' => 'date',
        'explicit'     => 'boolean',
    ];

    public function tracks()
    {
        return $this->hasMany(Music::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getStatusAttribute()
{
    // If ANY track is rejected → project rejected
    if ($this->tracks()->where('status', 'rejected')->exists()) {
        return 'rejected';
    }

    // If ANY track is pending → project pending
    if ($this->tracks()->where('status', 'pending')->exists()) {
        return 'pending';
    }

    // If ALL tracks are approved → project approved
    if ($this->tracks()->where('status', 'approved')->count() === $this->tracks()->count()) {
        return 'approved';
    }

    // Default
    return 'pending';
}

/**
 * Track-level copyright scan flags for the release.
 * Returns null when every track is clear, otherwise a list of
 * blocked tracks with the ACRCloud match details.
 */
public function getCopyrightFlagAttribute()
{
    $blocked = $this->tracks->filter(fn($track) => $track->status === 'blocked');

    if ($blocked->isEmpty()) {
        return null;
    }

    return $blocked->map(fn($track) => [
        'title'          => $track->title,
        'matched_title'  => $track->copyrightScan?->matched_title,
        'matched_artist' => $track->copyrightScan?->matched_artist,
    ])->all();
}

/**
 * Overall copyright scan state for the release.
 *
 * - 'blocked' → at least one track was matched
 * - 'error'   → a scan ran but failed (too large, no fingerprint, auth…)
 * - 'pending' → a track has not been scanned yet
 * - 'clean'   → every track was scanned and no match was found
 */
public function getScanStateAttribute()
{
    $tracks = $this->tracks;

    if ($tracks->contains(fn($track) => $track->status === 'blocked')) {
        return 'blocked';
    }

    if ($tracks->contains(fn($track) => $track->copyrightScan?->status === 'error')) {
        return 'error';
    }

    if ($tracks->contains(fn($track) => !$track->copyrightScan)) {
        return 'pending';
    }

    return 'clean';
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
    $mainArtists = $this->tracks->pluck('artist')->filter()->unique();

    $featuredArtists = $this->tracks
        ->pluck('featured_artists')
        ->filter()
        ->flatMap(fn ($value) => array_map('trim', explode(',', $value)))
        ->unique();

    if ($mainArtists->count() === 1) {
        $artist = $mainArtists->first();

        if ($featuredArtists->count()) {
            $artist .= ' feat. ' . $featuredArtists->join(', ');
        }

        return $artist;
    }

    return 'Various Artists';
}

}
