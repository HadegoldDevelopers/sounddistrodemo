<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Artist;
use App\Models\Label;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Update user table fields
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        /**
         * LABEL PROFILE
         */
        if ($user->role === 'label') {
            $label = $user->label ?? new Label(['user_id' => $user->id]);

            $label->label_name = $request->label_name;
            $label->description = $request->description;
            $label->website = $request->website;

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = uniqid() . '.' . $file->extension();

                $file->move(public_path('logos'), $filename);

                $label->logo = 'logos/' . $filename;
            }

            $label->save();
        }

        /**
         * ARTIST PROFILE
         */
        if ($user->role === 'artist') {
            $artist = $user->artist ?? new Artist(['user_id' => $user->id]);

            // Helper: normalize NIL
            $toNIL = function ($value) {
                if (empty($value)) {
                    return 'NIL';
                }
                return (strtolower(trim($value)) === 'nil') ? 'NIL' : $value;
            };

            // BASIC INFO
            $artist->name = $request->name;
            $artist->genre = $request->genre;
            $artist->bio = $request->bio;

            // MUSIC IDS
            $artist->audiomack_id = $toNIL($request->audiomack);
            $artist->spotify_id = $toNIL($request->spotify);
            $artist->apple_music_id = $toNIL($request->applemusic_id);

            // SOCIAL LINKS (stored as URL or NIL)
            $artist->facebook = $toNIL($request->facebook);
            $artist->instagram = $toNIL($request->instagram);
            $artist->tiktok = $toNIL($request->tiktok);
            $artist->twitter = $toNIL($request->twitter);

            // PROFILE IMAGE
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                
                $filename = uniqid() . '.' . $file->extension();

                $file->move(public_path('profile_images'), $filename);

                $artist->profile_image = 'profile_images/' . $filename;
            }

            $artist->save();
        }

        return Redirect::route('profile.edit')
            ->with('status', 'Your profile has been updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}