@extends('layouts.user')

@section('title', 'Add New Artist')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-zinc-800 rounded-lg shadow-lg text-gray-300">
    <h1 class="text-2xl font-semibold mb-6">
        {{ isset($artist) ? 'Edit Artist' : 'Add New Artist' }}
    </h1>

    <form action="{{ isset($artist) ? route('artists.update', $artist) : route('artists.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($artist))
            @method('PUT')
        @endif

        <div>
            <label for="name" class="block mb-1 font-semibold">Name <span class="text-orange-500">*</span></label>
            <input type="text" name="name" id="name" required
                value="{{ old('name', $artist->name ?? '') }}"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @error('name') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" id="email"
                value="{{ old('email', $artist->email ?? '') }}"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @error('email') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="genre" class="block mb-1 font-semibold">Genre</label>
            <input type="text" name="genre" id="genre"
                value="{{ old('genre', $artist->genre ?? '') }}"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @error('genre') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="audiomack_id" class="block mb-1 font-semibold">AudioMack ID</label>
            <input type="text" name="audiomack_id" id="audiomack_id"
                value="{{ old('audiomack_id', $artist->audiomack_id ?? '') }}"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>

        <div>
            <label for="spotify_id" class="block mb-1 font-semibold">Spotify ID</label>
            <input type="text" name="spotify_id" id="spotify_id"
                value="{{ old('spotify_id', $artist->spotify_id ?? '') }}"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>

        <div>
            <label for="apple_music_id" class="block mb-1 font-semibold">Apple Music ID</label>
            <input type="text" name="apple_music_id" id="apple_music_id"
                value="{{ old('apple_music_id', $artist->apple_music_id ?? '') }}"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>

        <div>
            <label for="facebook" class="block mb-1 font-semibold">Facebook URL</label>
            <input type="text" name="facebook" id="facebook"
                value="{{ old('facebook', $artist->facebook ?? '') }}"
                placeholder="https://facebook.com/... or NIL"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @error('facebook') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="instagram" class="block mb-1 font-semibold">Instagram URL</label>
            <input type="text" name="instagram" id="instagram"
                value="{{ old('instagram', $artist->instagram ?? '') }}"
                placeholder="https://instagram.com/... or NIL"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @error('instagram') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="tiktok" class="block mb-1 font-semibold">TikTok URL</label>
            <input type="text" name="tiktok" id="tiktok"
                value="{{ old('tiktok', $artist->tiktok ?? '') }}"
                placeholder="https://tiktok.com/... or NIL"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @error('tiktok') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="twitter" class="block mb-1 font-semibold">Twitter / X URL</label>
            <input type="text" name="twitter" id="twitter"
                value="{{ old('twitter', $artist->twitter ?? '') }}"
                placeholder="https://twitter.com/... or NIL"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @error('twitter') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile_image" class="block mb-1 font-semibold">Profile Image</label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            @if($artist->profile_image)
                <p class="text-xs text-gray-400 mt-1">Current: {{ $artist->profile_image }}</p>
            @endif
            @error('profile_image') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="bio" class="block mb-1 font-semibold">Bio</label>
            <textarea name="bio" id="bio" rows="4"
                class="w-full rounded-md border border-zinc-600 bg-zinc-900 px-3 py-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('bio', $artist->bio ?? '') }}</textarea>
        </div>

        <button type="submit"
            class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-5 py-2 rounded-lg transition">
            {{ isset($artist) ? 'Update Artist' : 'Add Artist' }}
        </button>
    </form>
</div>
@endsection
