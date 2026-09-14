<?php

namespace App\Helpers;

use App\Models\Artist;
use App\Models\User;

class ArtistMatcher
{
    public static function matchArtistUser(string $artistName, ?string $spotifyId = null, ?string $appleId = null): ?User
    {
        $artistName = trim($artistName);

        // 1. Try Spotify/Apple Music ID from artists table
        if ($spotifyId || $appleId) {
            $artist = Artist::query()
                ->when($spotifyId, fn($q) => $q->orWhere('spotify_id', $spotifyId))
                ->when($appleId, fn($q) => $q->orWhere('apple_music_id', $appleId))
                ->first();

            if ($artist && $artist->user && $artist->user->role === 'artist') {
                return $artist->user;
            }
        }

        // 2. Exact (case-insensitive) name match in artists table
        $artist = Artist::whereRaw('LOWER(name) = ?', [strtolower($artistName)])->first();
        if ($artist && $artist->user && $artist->user->role === 'artist') {
            return $artist->user;
        }

        // 3. Partial name match fallback
        $artist = Artist::where('name', 'like', '%' . $artistName . '%')->first();
        if ($artist && $artist->user && $artist->user->role === 'artist') {
            return $artist->user;
        }

        // 4. Final fallback: search users table directly (must be role = artist)
        return User::where('role', 'artist')
            ->whereRaw('LOWER(name) = ?', [strtolower($artistName)])
            ->first();
    }
}
