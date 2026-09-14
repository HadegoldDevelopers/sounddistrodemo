@extends('layouts.user')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-4xl mx-auto p-6 text-gray-100">
    <h2 class="text-3xl font-bold mb-6">Profile Settings</h2>

    {{-- Update Profile Info --}}
    <div class="bg-zinc-800 rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Update Profile</h3>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Full Name -->
            <div>
                <label class="block text-sm font-medium mb-1">Artist Name</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" readonly
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <!-- Role-specific fields -->
            @if(Auth::user()->role === 'artist')
            <!-- Profile Image -->
            <div>
                <label class="block text-sm font-medium mb-1">Profile Image</label>
                <input type="file" name="profile_image" accept="image/*"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <p class="text-xs text-gray-400 mt-1">Upload a new profile image (optional). JPG, PNG supported.</p>
            </div>
             <div>
                <label class="block text-sm font-medium mb-1">Genre</label>
        <input type="text" name="genre" value="{{ old('genre', Auth::user()->artist->genre) }}"
        class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Audiomack ID <span class="text-red-500">(required)</span></label>
                    <input type="text" name="audiomack" value="{{ old('audiomack', Auth::user()->artist->audiomack_id) }}"
                           class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Audiomack ID or type NIL">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Spotify ID <span class="text-red-500">(required)</span></label>
                    <input type="text" name="spotify" value="{{ old('spotify', Auth::user()->artist->spotify_id) }}"
                           class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Spotify ID or type NIL">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Apple Music ID <span class="text-red-500 text-sm">(required)</span></label>
                    <input type="text" name="applemusic_id" value="{{ old('applemusic_id', Auth::user()->artist->apple_music_id) }}"
                           class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Apple Music ID or type NIL">
                </div>
      {{-- Social Media Section --}}
        <h3 class="text-xl font-semibold mb-4">Social Media</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">
                    Facebook Link <span class="text-red-500">(required)</span></label>
                <input type="text" name="facebook" value="{{ old('facebook', Auth::user()->artist->facebook) }}"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Enter link or type NIL">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Instagram Link <span class="text-red-500">(required)</span></label>
                <input type="text" name="instagram" value="{{ old('instagram', Auth::user()->artist->instagram) }}"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Enter link or type NIL">
            </div>
 <div>
                <label class="block text-sm font-medium mb-1">TikTok Link <span class="text-red-500">(required)</span> </label>
                <input type="text" name="tiktok" value="{{ old('tiktok', Auth::user()->artist->tiktok) }}"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Enter link or type NIL">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">
                    Twitter Link</label>
                <input type="text" name="twitter" value="{{ old('twitter', Auth::user()->artist->twitter) }}"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Enter link or type NIL">
            </div>

           
        </div>
            <div>
    <label class="block text-sm font-medium mb-1">Bio</label>
    <textarea name="bio" rows="4"
        class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500"
        placeholder="Write a short bio about yourself...">{{ old('bio', Auth::user()->artist->bio) }}</textarea>
</div>

            @elseif(Auth::user()->role === 'label')
            <!-- Logo -->
            <div>
                <label class="block text-sm font-medium mb-1">Logo</label>
                <input type="file" name="logo" accept="image/*"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <p class="text-xs text-gray-400 mt-1">Upload a new profile image (optional). JPG, PNG supported.</p>
            </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Label Name</label>
                    <input type="text" name="label_name" value="{{ old('label_name', Auth::user()->label->label_name ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Website</label>
                    <input type="url" name="website" value="{{ old('website', Auth::user()->label->website ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter link">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description', Auth::user()->label->description ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            @endif

            <button type="submit"
                    class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                Save Changes
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-zinc-800 rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Change Password</h3>
        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Current Password</label>
                <input type="password" name="current_password"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" name="password"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-4 py-2 rounded-lg bg-zinc-700 text-white border border-zinc-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
