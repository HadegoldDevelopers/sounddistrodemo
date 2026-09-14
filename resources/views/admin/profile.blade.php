@extends('layouts.admin.app')

@section('title', 'Admin Profile')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h1 class="text-2xl font-bold mb-6">Admin Profile</h1>


    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf

        {{-- Name --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}" 
                class="w-full border px-3 py-2 rounded-lg @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}" 
                class="w-full border px-3 py-2 rounded-lg @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Password <span class="text-gray-400">(leave blank to keep current)</span></label>
            <input type="password" name="password" 
                class="w-full border px-3 py-2 rounded-lg @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" 
                class="w-full border px-3 py-2 rounded-lg">
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            Update Profile
        </button>
    </form>
</div>
@endsection