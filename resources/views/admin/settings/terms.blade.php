@extends('layouts.admin.app')

@section('title', 'Legal Documents')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Legal Documents</h2>

    @if(session('success'))
        <div class="mb-4 text-green-600 text-sm">
            {{ session('success') }}
        </div>
    @endif
    <form method="POST" action="{{ route('admin.settings.terms.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block font-medium mb-1">
                Terms & Conditions (PDF)
            </label>

            <input type="file" name="terms_pdf" accept="application/pdf"
                class="w-full border rounded px-3 py-2">

            <p class="text-sm text-gray-500 mt-1">
                Upload a PDF file (max 5MB).
            </p>

            @error('terms_pdf')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($termsPdf)
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Current file:</p>
                <a href="{{ asset($termsPdf) }}" target="_blank"
                   class="text-indigo-600 hover:underline">
                    View current Terms PDF
                </a>
            </div>
        @endif

        <button type="submit"
            class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700">
            Save Changes
        </button>
    </form>
</div>
@endsection
