<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use Illuminate\Support\Facades\Auth;

class ArtistLabelController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $artists = $user->label->artist()->latest()->paginate(10);

        return view('artists.index', compact('artists', 'user'));
    }

    public function create()
    {
        $user = Auth::user();

        return view('artists.create', compact('user'));
    }

    public function store(Request $request)
    {
        $this->normalizeNilFields($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:artists,email',
            'bio' => 'nullable|string',
            'genre' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:5120',

            // Music IDs
            'audiomack_id' => 'nullable|string|max:255',
            'spotify_id' => 'nullable|string|max:255',
            'apple_music_id' => 'nullable|string|max:255',

            // Social (allow NIL or URL)
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'twitter' => 'nullable|url',
        ]);

        $data['profile_image'] = $this->handleProfileImage($request);

        // Get label
        $label = Auth::user()->label;

        $data['label_id'] = $label->id;
        $data['user_id'] = Auth::id();

        Artist::create($data);

        return redirect()
            ->route('artists.index')
            ->with('success', 'Artist added successfully.');
    }

    public function edit(Artist $artist)
    {
        $this->authorizeArtist($artist);

        $user = Auth::user();

        return view('artists.edit', compact('artist', 'user'));
    }

    public function update(Request $request, Artist $artist)
    {
        $this->authorizeArtist($artist);

        $this->normalizeNilFields($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:artists,email,' . $artist->id,
            'bio' => 'nullable|string',
            'genre' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:5120',

            // Music IDs
            'audiomack_id' => 'nullable|string|max:255',
            'spotify_id' => 'nullable|string|max:255',
            'apple_music_id' => 'nullable|string|max:255',

            // Social fields (allow NIL or URL)
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'twitter' => 'nullable|url',
        ]);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $this->handleProfileImage($request);
        }

        $artist->update($data);

        return redirect()
            ->route('artists.index')
            ->with('success', 'Artist updated successfully.');
    }

    private function normalizeNilFields(Request $request): void
    {
        foreach (['facebook', 'instagram', 'tiktok', 'twitter'] as $field) {
            if (strtolower(trim((string) $request->input($field))) === 'nil') {
                $request->merge([$field => null]);
            }
        }
    }

    private function handleProfileImage(Request $request): ?string
    {
        if (!$request->hasFile('profile_image')) {
            return null;
        }

        $file = $request->file('profile_image');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('profile_images'), $filename);

        return 'profile_images/' . $filename;
    }

    private function authorizeArtist(Artist $artist): void
    {
        $user = Auth::user();

        $ownsArtist = $user->role === 'artist' && $artist->user_id === $user->id;
        $labelOwnsArtist = $user->role === 'label' && $user->label && $artist->label_id === $user->label->id;

        if (!$ownsArtist && !$labelOwnsArtist) {
            abort(403);
        }
    }
}