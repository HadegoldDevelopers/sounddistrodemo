@extends('layouts.user')

@section('title', 'New Release')

@section('content')
<div class="max-w-4xl mx-auto bg-zinc-900 text-white p-8 rounded-lg shadow-xl mt-12">
    <h2 class="text-2xl font-bold text-orange-500 mb-6 flex items-center">
        <i class="fas fa-upload mr-2"></i> New Release
    </h2>

    <form id="musicUploadForm" enctype="multipart/form-data" action="{{ route('music.store') }}" method="POST" class="space-y-6">
        @csrf

        <input type="hidden" name="cover_path" id="cover_path">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Project Title --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Track / Album Title</label>
                <input type="text" name="title" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2" required>
            </div>
            {{-- Primary Artist --}}
@if($user->role === 'label')
    <div>
        <label class="block mb-1 text-sm font-medium">Primary Artist</label>

        <select name="artist_id" id="artistSelect"
                class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            @forelse($user->label->artist as $artist)
                <option value="{{ $artist->id }}" data-complete="{{ $artist->isProfileComplete() ? 1 : 0 }}">
                    {{ $artist->name }} @if(!$artist->isProfileComplete()) (Incomplete Profile) @endif
                </option>
            @empty
                <option disabled>No artists found</option>
            @endforelse
        </select>

        <p id="artistWarning" class="text-red-500 text-sm mt-1 hidden">
            Selected artist profile is incomplete. Please complete the artist profile first.
        </p>
    </div>
@endif


@if($user->role === 'artist')
    <div>
        <label class="block mb-1 text-sm font-medium">Primary Artist</label>
<input type="text" value="{{ $user->artist->name}}"readonly class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
 <input type="hidden" name="artist" value="{{ $user->artist->name }}">
    </div>
@endif
            {{-- Music Type --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Music Type</label>
                <select name="type" id="musicType" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
                    <option value="Single" selected>Single</option>
                    <option value="EP">EP</option>
                    <option value="Album">Album</option>
                </select>
            </div>

            {{-- Track Count (EP/Album only) --}}
            <div id="trackCountWrapper" class="hidden">
                <label class="block mb-1 text-sm font-medium">Number of Tracks</label>
                <input type="number" id="trackCountInput" min="1" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
                <p class="text-xs text-gray-400 mt-1" id="trackCountHint"></p>
            </div>

            {{-- Genre --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Genre</label>
                <input type="text" name="genre" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2" required>
            </div>

            {{-- Subgenre --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Subgenre (optional)</label>
                <input type="text" name="subgenre" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            </div>

            {{-- Release Date --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Release Date</label>
<input type="date" name="release_date" required min="{{ now()->addDays(14)->format('Y-m-d') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">

            </div>

            {{-- Language --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Language</label>
                <input type="text" name="language" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            </div>

            {{-- Explicit --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Explicit Lyrics?</label>
                <select name="explicit" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select>
            </div>

            {{-- Label --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Label / Publisher</label>
                <input type="text" name="label" placeholder="separated with commas if more than one" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            </div>

            {{-- Songwriter --}}
            <div>
                <label class="block mb-1 text-sm font-medium">Songwriter / Composer</label>
                <input type="text" placeholder="separated with commas if more than one" name="songwriter" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            </div>

            {{-- ISRC --}}
            <div>
                <label class="block mb-1 text-sm font-medium">ISRC (optional)</label>
                <input type="text" name="isrc" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            </div>

            {{-- UPC --}}
            <div>
                <label class="block mb-1 text-sm font-medium">UPC (optional)</label>
                <input type="text" name="upc" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            </div>

            {{-- Featured Artists (Single only) --}}
            <div id="singleFeaturedWrapper">
                <label class="block mb-1 text-sm font-medium">Featured Artist(s)</label>
                <input type="text" name="featured_artists" placeholder="separated with commas if more than one" class="w-full bg-zinc-800 border border-zinc-700 rounded px-4 py-2">
            </div>
{{-- Cover Image --}}
<div class="col-span-1 md:col-span-2">
    <label class="block mb-1 text-sm font-medium">
        Cover Image <span class="text-red-500">*</span>
        <span class="text-xs text-gray-400 ml-2">(JPG, min 3000x3000px, max 5MB)</span>
    </label>

    <div id="coverDropZone"
         class="border-2 border-dashed border-zinc-600 rounded-lg p-6 text-center cursor-pointer hover:border-orange-500 transition">

        {{-- Default state --}}
        <div id="coverDefault">
            <i class="fas fa-image text-3xl text-gray-500 mb-2"></i>
            <p class="text-sm text-gray-400">Click or drag & drop your cover image here</p>
            <p class="text-xs text-gray-500 mt-1">JPG ONLY minimum 3000 x 3000px</p>
        </div>

        {{-- Uploading state --}}
        <div id="coverUploading" class="hidden">
            <i class="fas fa-spinner fa-spin text-2xl text-orange-400 mb-2"></i>
            <p class="text-sm text-orange-400">Uploading cover...</p>
        </div>

        {{-- Success state --}}
        <div id="coverSuccess" class="hidden">
            <img id="coverPreview" class="w-32 h-32 object-cover rounded shadow-md mx-auto mb-2">
            <p class="text-sm text-green-400"><i class="fas fa-check-circle mr-1"></i> Cover uploaded</p>
            <p class="text-xs text-gray-400 mt-1">Click to change</p>
        </div>

        {{-- Error state --}}
        <div id="coverError" class="hidden">
            <i class="fas fa-exclamation-circle text-2xl text-red-400 mb-2"></i>
            <p id="coverErrorMsg" class="text-sm text-red-400"></p>
            <p class="text-xs text-gray-400 mt-1">Click to try again</p>
        </div>

        <input type="file" id="coverInput" accept="image/jpeg,image/png,image/webp"
               class="hidden">
    </div>
</div>
 {{-- OLD SINGLE AUDIO INPUT (HIDDEN FOREVER) --}}
            <div id="singleAudioWrapper" class="hidden"></div>
        </div>

        {{-- TRACKS SECTION --}}
        <div id="tracksSection" class="hidden mt-8">
            <h3 class="text-xl font-semibold text-orange-400 mb-3">Tracks</h3>

            <div id="tracksContainer" class="space-y-6"></div>

            <button type="button" id="addTrackBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                + Add Track
            </button>
        </div>

        <div class="pt-4">
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md">
                <i class="fas fa-cloud-upload-alt mr-2"></i>Submit New Release
            </button>
        </div>

    </form>
</div>
@endsection
@push('scripts')
<script>
        const artistSelect = document.getElementById('artistSelect');
        const submitBtn = document.querySelector('button[type="submit"]');
        const warning = document.getElementById('artistWarning');

        function checkArtistProfile() {
            const selected = artistSelect.options[artistSelect.selectedIndex];
            const complete = selected.dataset.complete === '1';

            if (!complete) {
                warning.classList.remove('hidden');
                submitBtn.disabled = true;
            } else {
                warning.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }

        // Initial check
        checkArtistProfile();

        // Check whenever selection changes
        artistSelect.addEventListener('change', checkArtistProfile);
    </script>
<script>
/* ---------------------------------------------------------
   ELEMENTS
--------------------------------------------------------- */
const musicType = document.getElementById('musicType');
const trackCountWrapper = document.getElementById('trackCountWrapper');
const trackCountInput = document.getElementById('trackCountInput');
const trackCountHint = document.getElementById('trackCountHint');

const singleFeaturedWrapper = document.getElementById('singleFeaturedWrapper');
const singleAudioWrapper = document.getElementById('singleAudioWrapper');

const tracksSection = document.getElementById('tracksSection');
const tracksContainer = document.getElementById('tracksContainer');
const addTrackBtn = document.getElementById('addTrackBtn');

let trackIndex = 0;

/* ---------------------------------------------------------
   RESET TRACKS
--------------------------------------------------------- */
function resetTracks() {
    tracksContainer.innerHTML = '';
    trackIndex = 0;
}

/* ---------------------------------------------------------
   UPDATE ADD TRACK VISIBILITY
--------------------------------------------------------- */
function updateAddTrackVisibility() {
    const max = parseInt(trackCountInput.value || '1', 10);

    if (trackIndex >= max) {
        addTrackBtn.classList.add('hidden');
    } else {
        addTrackBtn.classList.remove('hidden');
    }
}

/* ---------------------------------------------------------
   BUILD EXACT NUMBER OF TRACKS
--------------------------------------------------------- */
function buildTracks(count) {
    resetTracks();
    for (let i = 0; i < count; i++) {
        addTrack();
    }
    updateAddTrackVisibility();
}

/* ---------------------------------------------------------
   MUSIC TYPE CHANGE
--------------------------------------------------------- */
musicType.addEventListener('change', () => {
    const type = musicType.value;

    resetTracks();

    if (type === 'Single') {
        trackCountWrapper.classList.add('hidden');
        addTrackBtn.classList.add('hidden');

        singleFeaturedWrapper.classList.remove('hidden');
        singleAudioWrapper.classList.add('hidden');

        tracksSection.classList.remove('hidden');
        buildTracks(1);
        return;
    }

    // EP or Album
    singleFeaturedWrapper.classList.add('hidden');
    singleAudioWrapper.classList.add('hidden');

    tracksSection.classList.remove('hidden');
    trackCountWrapper.classList.remove('hidden');

    if (type === 'EP') {
        trackCountHint.textContent = 'Maximum of 7 tracks for an EP.';
        trackCountInput.max = 7;
        trackCountInput.value = 1;
        buildTracks(1);
    } else {
        trackCountHint.textContent = 'Enter any number of tracks.';
        trackCountInput.removeAttribute('max');
        trackCountInput.value = 1;
        buildTracks(1);
    }
});

/* ---------------------------------------------------------
   TRACK COUNT CHANGE
--------------------------------------------------------- */
trackCountInput.addEventListener('input', () => {
    let count = parseInt(trackCountInput.value || '1', 10);

    if (count < 1) count = 1;

    if (musicType.value === 'EP' && count > 7) {
        alert('EP can only have up to 7 tracks.');
        count = 7;
        trackCountInput.value = 7;
    }

    buildTracks(count);
});

/* ---------------------------------------------------------
   ADD TRACK BUTTON
--------------------------------------------------------- */
addTrackBtn.addEventListener('click', () => {
    const max = parseInt(trackCountInput.value || '1', 10);

    if (trackIndex >= max) {
        addTrackBtn.classList.add('hidden');
        return;
    }

    addTrack();
    updateAddTrackVisibility();
});

/* ---------------------------------------------------------
   ADD TRACK
--------------------------------------------------------- */
function addTrack() {
    trackIndex++;

    const trackHtml = `
        <div class="p-4 bg-zinc-800 rounded-lg border border-zinc-700" data-track-index="${trackIndex}">
            <h4 class="font-semibold text-orange-300 mb-2">Track ${trackIndex}</h4>

            <label class="block mb-1 text-sm font-medium">Track Title</label>
            <input type="text" name="tracks[${trackIndex}][title]" class="w-full bg-zinc-700 border border-zinc-600 rounded px-3 py-2 mb-3" required>

            <label class="block mb-1 text-sm font-medium">Featured Artist(s)</label>
            <input type="text" name="tracks[${trackIndex}][featured_artists]" placeholder="separated with commas if more than one" class="w-full bg-zinc-700 border border-zinc-600 rounded px-3 py-2 mb-3">
            <label class="block mb-1 text-sm font-medium">Producer(s)</label>
            <input type="text" name="tracks[${trackIndex}][credits]" placeholder="separated with commas if more than one" class="w-full bg-zinc-700 border border-zinc-600 rounded px-3 py-2 mb-3">
            
            <label class="block mb-1 text-sm font-medium">ISRC (optional)</label>
            <input type="text" name="tracks[${trackIndex}][isrc]" class="w-full bg-zinc-700 border border-zinc-600 rounded px-3 py-2 mb-3">

            <input type="hidden" name="tracks[${trackIndex}][audio_path]" class="track-audio-path">

            <label class="block mb-1 text-sm font-medium">Audio File (MP3/WAV)</label>
            <input type="file" class="track-audio-file w-full bg-zinc-700 border border-zinc-600 rounded px-3 py-2 mb-2" accept=".mp3,.wav">

            <div class="w-full bg-zinc-900 rounded h-2 mb-2">
                <div class="track-progress h-2 bg-orange-500 rounded" style="width:0%"></div>
            </div>

            <p class="track-status text-xs text-gray-400 mb-2">No upload yet.</p>

            <button type="button" class="uploadTrackBtn bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded mb-2">
                Upload Track
            </button>

            <button type="button" class="removeTrackBtn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                Remove Track
            </button>
        </div>
    `;

    tracksContainer.insertAdjacentHTML('beforeend', trackHtml);

    document.querySelectorAll('.removeTrackBtn').forEach(btn => {
        btn.onclick = () => {
            btn.parentElement.remove();
            trackIndex--;
            updateAddTrackVisibility();
        };
    });

    document.querySelectorAll('.uploadTrackBtn').forEach(btn => {
        btn.onclick = () => {
            const wrapper = btn.closest('[data-track-index]');
            const idx = wrapper.getAttribute('data-track-index');
            startChunkUpload(wrapper, idx);
        };
    });
}

/* ---------------------------------------------------------
   CHUNK UPLOAD
--------------------------------------------------------- */
async function startChunkUpload(wrapper, trackIndex) {
    const fileInput = wrapper.querySelector('.track-audio-file');
    const progressBar = wrapper.querySelector('.track-progress');
    const statusText = wrapper.querySelector('.track-status');
    const hiddenPath = wrapper.querySelector('.track-audio-path');
    const projectTitleInput = document.querySelector('input[name="title"]');

    const file = fileInput.files[0];
    if (!file) return alert('Select an audio file first.');

    const projectTitle = projectTitleInput.value.trim();
    if (!projectTitle) return alert('Enter project title first.');

    const originalName = file.name;
    let chunkSize = 1024 * 1024 * 2;
    const totalSize = file.size;
    const totalChunks = Math.ceil(totalSize / chunkSize);
    let currentChunk = 0;

    statusText.textContent = 'Uploading...';
    statusText.classList.remove('text-red-400');

    while (currentChunk < totalChunks) {
        const start = currentChunk * chunkSize;
        const end = Math.min(start + chunkSize, totalSize);
        const blob = file.slice(start, end);

        const formData = new FormData();
        formData.append('project_title', projectTitle);
        formData.append('track_index', trackIndex);
        formData.append('chunk_number', currentChunk + 1);
        formData.append('total_chunks', totalChunks);
        formData.append('original_name', originalName);
        formData.append('chunk', blob);

        try {
            const res = await fetch("{{ route('music.uploadChunk') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            if (!res.ok) {
                if (res.status === 419) {
                    throw new Error('Session expired — refresh the page and try again.');
                }
                throw new Error('Upload failed (HTTP ' + res.status + ').');
            }

            const data = await res.json();

            const percent = Math.round(((currentChunk + 1) / totalChunks) * 100);
            progressBar.style.width = percent + '%';
            statusText.textContent = 'Uploading... ' + percent + '%';

            if (data.done) {
                hiddenPath.value = data.path;
                statusText.textContent = 'Upload complete.';
                break;
            }
        } catch (err) {
            statusText.textContent = 'Upload failed: ' + err.message;
            statusText.classList.add('text-red-400');
            progressBar.style.width = '0%';
            return;
        }

        currentChunk++;
    }
}

/* ---------------------------------------------------------
   COVER UPLOAD — Auto upload on select, disable submit until done
--------------------------------------------------------- */
const coverDropZone    = document.getElementById('coverDropZone');
const coverInput       = document.getElementById('coverInput');
const coverDefault     = document.getElementById('coverDefault');
const coverUploading   = document.getElementById('coverUploading');
const coverSuccess     = document.getElementById('coverSuccess');
const coverError       = document.getElementById('coverError');
const coverErrorMsg    = document.getElementById('coverErrorMsg');
const coverPreview     = document.getElementById('coverPreview');
const coverPathInput   = document.getElementById('cover_path');
let coverUploaded      = false;

// Click to open file picker
coverDropZone.addEventListener('click', () => coverInput.click());

// Drag and drop
coverDropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    coverDropZone.classList.add('border-orange-500');
});
coverDropZone.addEventListener('dragleave', () => {
    coverDropZone.classList.remove('border-orange-500');
});
coverDropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    coverDropZone.classList.remove('border-orange-500');
    const file = e.dataTransfer.files[0];
    if (file) handleCoverFile(file);
});

// File input change
coverInput.addEventListener('change', () => {
    const file = coverInput.files[0];
    if (file) handleCoverFile(file);
});

function showCoverState(state) {
    coverDefault.classList.add('hidden');
    coverUploading.classList.add('hidden');
    coverSuccess.classList.add('hidden');
    coverError.classList.add('hidden');
    document.getElementById('cover' + state).classList.remove('hidden');
}

async function handleCoverFile(file) {
    // Validate type
    if (!['image/jpeg', 'image/jpeg'].includes(file.type)) {
        showCoverState('Error');
        coverErrorMsg.textContent = 'Only JPG/JPEG images are allowed.';
        return;
    }

    // Validate size — max 5MB
    if (file.size > 5 * 1024 * 1024) {
        showCoverState('Error');
        coverErrorMsg.textContent = 'Cover image must be under 5MB.';
        return;
    }

    // Validate dimensions
    const img = new Image();
    const objectUrl = URL.createObjectURL(file);
    img.onload = async () => {
        URL.revokeObjectURL(objectUrl);

        if (img.width < 3000 || img.height < 3000) {
            showCoverState('Error');
            coverErrorMsg.textContent = `Cover must be at least 3000x3000px. Yours is ${img.width}x${img.height}px.`;
            return;
        }

        // All good — upload
        showCoverState('Uploading');
        coverUploaded = false;
        submitBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('cover', file);

            const res = await fetch("{{ route('music.uploadCover') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const result = await res.json();

            if (result.path) {
                coverPathInput.value = result.path;
                coverPreview.src = result.url;
                coverUploaded = true;
                showCoverState('Success');
                submitBtn.disabled = false;
            } else {
                throw new Error(result.message || 'Upload failed');
            }
        } catch (err) {
            showCoverState('Error');
            coverErrorMsg.textContent = 'Upload failed. Click to try again.';
        }
    };

    img.onerror = () => {
        showCoverState('Error');
        coverErrorMsg.textContent = 'Invalid image file.';
    };

    img.src = objectUrl;
}
/* ---------------------------------------------------------
   VALIDATION BEFORE SUBMIT
--------------------------------------------------------- */
document.getElementById('musicUploadForm').addEventListener('submit', function (e) {
    // Cover check
    if (!coverUploaded || !coverPathInput.value) {
        e.preventDefault();
        // Scroll to cover and highlight it
        coverDropZone.classList.add('border-red-500');
        coverDropZone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        showCoverState('Error');
        coverErrorMsg.textContent = 'Please upload a cover image before submitting.';
        return false;
    }

    // Tracks check
    const trackWrappers = document.querySelectorAll('#tracksContainer [data-track-index]');
    for (const wrapper of trackWrappers) {
        const hiddenPath = wrapper.querySelector('.track-audio-path');
        if (!hiddenPath.value) {
            e.preventDefault();
            wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert('Please upload audio for all tracks before submitting.');
            return false;
        }
    }

    // Disable submit to prevent double submission
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
});
/* ---------------------------------------------------------
   INITIALIZE DEFAULT (Single)
--------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    musicType.dispatchEvent(new Event('change'));
});
</script>
@endpush
