@extends('layouts.admin.app')
@section('title', 'Refund Policy')

@section('content')
<div class="w-full max-w-full lg:max-w-5xl mx-auto px-4 py-6 space-y-6">

    <div>
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Refund Policy</h2>
        <p class="text-sm text-gray-600 mt-1">Edit the refund policy displayed to users.</p>
    </div>

    <form id="refundForm" method="POST" action="{{ route('admin.settings.refund.update') }}">
        @csrf

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Policy Content</label>

                <textarea id="refund_policy" name="refund_policy" class="hidden">{{ old('refund_policy', $refund) }}</textarea>

                <div x-ignore>
                    <div id="refund_editor" class="min-h-[500px]"></div>
                </div>

                @error('refund_policy')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                    Save Refund Policy
                </button>
            </div>

        </div>
    </form>
</div>

{{-- Quill --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const quill = new Quill('#refund_editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        },
        placeholder: 'Write your refund policy here...',
    });

    const existing = document.getElementById('refund_policy').value;
    quill.clipboard.dangerouslyPasteHTML(existing);

    const form = document.getElementById('refundForm');

    form.addEventListener('submit', function () {
        document.getElementById('refund_policy').value = quill.root.innerHTML;
    });
});
</script>
@endsection
