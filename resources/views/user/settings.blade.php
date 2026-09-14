@extends('layouts.user')

@section('title', 'User Settings')
@section('content')
<div class="max-w-3xl mx-auto p-6 bg-zinc-900 rounded-lg shadow space-y-6 mt-8">

    <h2 class="text-2xl font-bold text-white">Settings</h2>

    <form action="{{ route('user.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Email Notifications -->
        <div class="flex items-center space-x-3">
            <input type="hidden" name="email_notifications" value="0">
            <input type="checkbox" name="email_notifications" id="email_notifications" value="1"
                {{ old('email_notifications', auth()->user()->email_notifications) ? 'checked' : '' }}>
            <label for="email_notifications" class="text-gray-300">Enable Email Notifications</label>
        </div>

        <!-- Dark Mode -->
        <div class="flex items-center space-x-3">
            <input type="hidden" name="dark_mode" value="0">
            <input type="checkbox" name="dark_mode" id="dark_mode" value="1"
                {{ old('dark_mode', auth()->user()->dark_mode) ? 'checked' : '' }}>
            <label for="dark_mode" class="text-gray-300">Enable Dark Mode</label>
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition duration-200">
            Save Settings
        </button>
    </form>
</div>
@endsection
