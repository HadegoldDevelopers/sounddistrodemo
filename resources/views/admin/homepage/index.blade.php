@extends('layouts.admin.app')

@section('title', 'Homepage Content')

@section('content')
<div class="text-gray-900 p-4 lg:p-0"
     x-data="homepageContent(@js($homepage?->features ?? []), @js($artists))">

  <div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold">Homepage Content</h1>
  </div>

  {{-- Section Tabs --}}
  <div class="mb-6">
    <select x-model="tab" aria-label="Sections"
            class="w-full md:hidden mb-4 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
      <template x-for="t in tabs" :key="t.key">
        <option :value="t.key" x-text="t.label"></option>
      </template>
    </select>

    <div class="hidden md:flex flex-wrap gap-2 border-b border-gray-300">
      <template x-for="t in tabs" :key="t.key">
        <button type="button" @click="tab = t.key"
                :class="tab === t.key ? 'border-orange-500 text-orange-500' : 'border-transparent text-gray-600 hover:text-orange-500'"
                class="py-2 px-4 font-semibold border-b-2 transition-colors"
                x-text="t.label"></button>
      </template>
    </div>
  </div>

  <form id="homepageForm" method="POST" action="{{ route('admin.homepage.update') }}" enctype="multipart/form-data">
    @csrf

    {{-- Hero --}}
    <section x-show="tab === 'hero'" x-cloak class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Hero Section</h2>

      <div class="mb-4">
        <label for="hero_title" class="block font-medium mb-1">Hero Title</label>
        <input type="text" name="hero_title" id="hero_title" value="{{ old('hero_title', $homepage?->hero_title) }}"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        @error('hero_title')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
      </div>

      <div class="mb-4">
        <label for="hero_text" class="block font-medium mb-1">Hero Text</label>
        <textarea name="hero_text" id="hero_text" rows="3"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('hero_text', $homepage?->hero_text) }}</textarea>
        @error('hero_text')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block font-medium mb-2">Hero Image</label>
        @if(!empty($homepage?->hero_image))
          <img src="{{ asset($homepage->hero_image) }}" alt="Hero Image" class="h-24 mb-2 rounded-lg border border-gray-300">
        @else
          <p class="text-gray-400 italic mb-2">No image uploaded yet.</p>
        @endif
        <input type="file" name="hero_image" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:bg-orange-500 file:text-white hover:file:bg-orange-600"
               onchange="previewImage(event, 'heroImagePreview')">
        <img id="heroImagePreview" src="#" alt="Hero Preview" class="mt-4 hidden h-24 rounded-lg border border-gray-300">
        @error('hero_image')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
      </div>
    </section>

    {{-- Features --}}
    <section x-show="tab === 'features'" x-cloak class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Features Section</h2>
      <p class="text-sm text-gray-500 mb-4">The features band and the feature grid are both built from the list below. Rows with a title appear in the grid, all rows appear in the top band.</p>

      <div class="mb-4">
        <label for="features_title" class="block font-medium mb-1">Features Title</label>
        <input type="text" name="features_title" id="features_title" value="{{ old('features_title', $homepage?->features_title) }}"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
      </div>

      <div class="mb-6">
        <label for="features_text" class="block font-medium mb-1">Features Description</label>
        <textarea name="features_text" id="features_text" rows="2"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('features_text', $homepage?->features_text) }}</textarea>
      </div>

      <div class="flex items-center justify-between mb-3">
        <label class="block font-medium">Feature Items</label>
        <button type="button" @click="addFeature()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-1.5 px-4 rounded-lg transition">
          + Add Feature
        </button>
      </div>

      <div class="space-y-4">
        <template x-for="(feature, i) in features" :key="i">
          <div class="border border-gray-200 rounded-lg p-4 space-y-3">
            <div class="grid md:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium mb-1">Icon (Font Awesome class)</label>
                <input type="text" x-model="feature.icon" :name="`features[${i}][icon]`" placeholder="fa-music"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Title (shown in grid)</label>
                <input type="text" x-model="feature.title" :name="`features[${i}][title]`"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Text</label>
              <input type="text" x-model="feature.text" :name="`features[${i}][text]`"
                     class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <button type="button" @click="removeFeature(i)" class="text-red-500 text-sm font-medium hover:text-red-600">
              Remove
            </button>
          </div>
        </template>
      </div>
      @error('features')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
    </section>

    {{-- Labels --}}
    <section x-show="tab === 'labels'" x-cloak class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Labels Section</h2>

      <div class="mb-4">
        <label for="labels_title" class="block font-medium mb-1">Labels Title</label>
        <input type="text" name="labels_title" id="labels_title" value="{{ old('labels_title', $homepage?->labels_title) }}"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
      </div>

      <div class="mb-4">
        <label for="labels_text" class="block font-medium mb-1">Labels Text</label>
        <textarea name="labels_text" id="labels_text" rows="3"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('labels_text', $homepage?->labels_text) }}</textarea>
      </div>

      <div>
        <label class="block font-medium mb-2">Labels Image</label>
        @if(!empty($homepage?->labels_image))
          <img src="{{ asset($homepage->labels_image) }}" alt="Labels Image" class="h-24 mb-2 rounded-lg border border-gray-300">
        @else
          <p class="text-gray-400 italic mb-2">No image uploaded yet.</p>
        @endif
        <input type="file" name="labels_image" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:bg-orange-500 file:text-white hover:file:bg-orange-600"
               onchange="previewImage(event, 'labelsImagePreview')">
        <img id="labelsImagePreview" src="#" alt="Labels Preview" class="mt-4 hidden h-24 rounded-lg border border-gray-300">
        @error('labels_image')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
      </div>
    </section>

    {{-- Pricing --}}
    <section x-show="tab === 'pricing'" x-cloak class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Pricing Section</h2>
      <p class="text-sm text-gray-500 mb-4">Plans themselves are managed under Subscription Management.</p>

      <div class="mb-4">
        <label for="pricing_title" class="block font-medium mb-1">Pricing Title</label>
        <input type="text" name="pricing_title" id="pricing_title" value="{{ old('pricing_title', $homepage?->pricing_title) }}"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
      </div>

      <div>
        <label for="pricing_text" class="block font-medium mb-1">Pricing Text</label>
        <textarea name="pricing_text" id="pricing_text" rows="2"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('pricing_text', $homepage?->pricing_text) }}</textarea>
      </div>
    </section>

    {{-- Artists --}}
    <section x-show="tab === 'artists'" x-cloak class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Featured Artists</h2>

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
        <label class="block font-medium">Artist Items</label>
        <button type="button" @click="addArtist()"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-1.5 px-4 rounded-lg transition">
          + Add Artist
        </button>
      </div>

      <div class="space-y-4">
        <template x-for="(artist, i) in artists" :key="i">
          <div class="border border-gray-200 rounded-lg p-4 space-y-4">
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium text-gray-700">Artist <span x-text="i + 1"></span></label>
              <button type="button" @click="removeArtist(i)" class="text-red-500 text-sm font-medium hover:text-red-600">
                Remove
              </button>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
              <div class="flex items-center gap-3 sm:flex-col sm:items-center shrink-0">
                <img x-show="artist.preview || artist.image"
                     :src="artist.preview || artist.image"
                     alt="Artist Image"
                     class="h-16 w-16 object-cover rounded-lg border border-gray-300 shrink-0">
                <div x-show="!(artist.preview || artist.image)"
                     class="h-16 w-16 rounded-lg border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-gray-400 text-xs text-center">
                  No image
                </div>
                <input type="file" :name="`artists[${i}][image]`" accept="image/jpeg,image/png,image/webp"
                       class="w-full sm:w-40 text-sm file:mr-2 file:py-1.5 file:px-3 file:rounded file:bg-orange-500 file:text-white hover:file:bg-orange-600"
                       @change="previewArtist($event, artist)">
              </div>

              <div class="flex-1 space-y-3 min-w-0">
                <div class="grid sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" x-model="artist.name" :name="`artists[${i}][name]`"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Genre</label>
                    <input type="text" x-model="artist.genre" :name="`artists[${i}][genre]`"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Overlay Color</label>
                  <div class="flex items-center gap-2">
                    <input type="color" x-model="artist.overlay_color" :name="`artists[${i}][overlay_color]`"
                           class="h-10 w-16 border rounded cursor-pointer">
                    <span class="text-sm text-gray-500" x-text="artist.overlay_color"></span>
                  </div>
                </div>
              </div>
            </div>

            <input type="hidden" x-model="artist.current_image" :name="`artists[${i}][current_image]`">
          </div>
        </template>
      </div>
      @error('artists')<p class="text-red-500 mt-1 text-sm">{{ $message }}</p>@enderror
    </section>

    {{-- CTA --}}
    <section x-show="tab === 'cta'" x-cloak class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Call to Action</h2>

      <div class="grid md:grid-cols-2 gap-4 mb-4">
        <div>
          <label for="cta_title_1" class="block font-medium mb-1">Title Line 1</label>
          <input type="text" name="cta_title_1" id="cta_title_1" value="{{ old('cta_title_1', $homepage?->cta_title_1) }}"
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div>
          <label for="cta_title_2" class="block font-medium mb-1">Title Line 2</label>
          <input type="text" name="cta_title_2" id="cta_title_2" value="{{ old('cta_title_2', $homepage?->cta_title_2) }}"
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
      </div>

      <div>
        <label for="cta_text" class="block font-medium mb-1">Call to Action Text</label>
        <textarea name="cta_text" id="cta_text" rows="2"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('cta_text', $homepage?->cta_text) }}</textarea>
      </div>
    </section>

    <div class="flex sm:justify-end">
      <button type="submit"
              class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg transition">
        Save Changes
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  function homepageContent(features, artists) {
    return {
      tab: 'hero',
      tabs: [
        { key: 'hero', label: 'Hero' },
        { key: 'features', label: 'Features' },
        { key: 'labels', label: 'Labels' },
        { key: 'pricing', label: 'Pricing' },
        { key: 'artists', label: 'Artists' },
        { key: 'cta', label: 'Call to Action' },
      ],
      features: features.map(f => ({
        icon: f.icon ?? '',
        title: f.title ?? '',
        text: f.text ?? '',
      })),
      artists: artists.map(a => ({
        name: a.name ?? '',
        genre: a.genre ?? '',
        overlay_color: a.overlay_color ?? '#000000',
        image: a.image_url ?? '',
        current_image: a.image ?? '',
        preview: '',
      })),
      addFeature() {
        this.features.push({ icon: '', title: '', text: '' });
      },
      removeFeature(i) {
        this.features.splice(i, 1);
      },
      addArtist() {
        this.artists.push({ name: '', genre: '', overlay_color: '#000000', image: '', current_image: '', preview: '' });
      },
      removeArtist(i) {
        this.artists.splice(i, 1);
      },
      previewArtist(event, artist) {
        const file = event.target.files[0];
        if (!file) {
          artist.preview = '';
          return;
        }
        const reader = new FileReader();
        reader.onload = e => { artist.preview = e.target.result; };
        reader.readAsDataURL(file);
      },
    };
  }

  function previewImage(event, previewId) {
    const input = event.target;
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endpush