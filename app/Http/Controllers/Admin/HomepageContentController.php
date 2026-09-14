<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageContent;
use Illuminate\Http\Request;

class HomepageContentController extends Controller
{
    public function index()
    {
        $homepage = HomepageContent::first();

        $artists = collect($homepage?->artists ?? [])
            ->map(fn($artist) => [
                ...$artist,
                'image_url' => !empty($artist['image']) ? asset($artist['image']) : '',
            ])
            ->all();

        return view('admin.homepage.index', compact('homepage', 'artists'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_text' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',

            'features_title' => 'nullable|string|max:255',
            'features_text' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*.icon' => 'nullable|string|max:100',
            'features.*.title' => 'nullable|string|max:255',
            'features.*.text' => 'nullable|string',

            'labels_title' => 'nullable|string|max:255',
            'labels_text' => 'nullable|string',
            'labels_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',

            'pricing_title' => 'nullable|string|max:255',
            'pricing_text' => 'nullable|string',

            'artists' => 'nullable|array',
            'artists.*.name' => 'nullable|string|max:255',
            'artists.*.genre' => 'nullable|string|max:255',
            'artists.*.overlay_color' => 'nullable|string|max:20',
            'artists.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',

            'cta_title_1' => 'nullable|string|max:255',
            'cta_title_2' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string',
        ]);

        $data = [
            'hero_title' => $validated['hero_title'] ?? null,
            'hero_text' => $validated['hero_text'] ?? null,
            'features_title' => $validated['features_title'] ?? null,
            'features_text' => $validated['features_text'] ?? null,
            'labels_title' => $validated['labels_title'] ?? null,
            'labels_text' => $validated['labels_text'] ?? null,
            'pricing_title' => $validated['pricing_title'] ?? null,
            'pricing_text' => $validated['pricing_text'] ?? null,
            'cta_title_1' => $validated['cta_title_1'] ?? null,
            'cta_title_2' => $validated['cta_title_2'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
        ];

        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $this->storeImage($request->file('hero_image'), 'hero');
        }

        if ($request->hasFile('labels_image')) {
            $data['labels_image'] = $this->storeImage($request->file('labels_image'), 'labels');
        }

        $data['features'] = $this->normalizeFeatures($request->input('features', []));
        $data['artists'] = $this->normalizeArtists($request);

        $homepage = HomepageContent::firstOrNew([]);
        $homepage->fill($data)->save();

        return redirect()->route('admin.homepage.index')->with('success', 'Homepage content updated successfully.');
    }

    private function normalizeFeatures(array $rows): array
    {
        return array_values(array_filter(
            array_map(
                fn($row) => [
                    'icon' => $row['icon'] ?? '',
                    'title' => $row['title'] ?? '',
                    'text' => $row['text'] ?? '',
                ],
                $rows
            ),
            fn($row) => $row['icon'] !== '' || $row['title'] !== '' || $row['text'] !== ''
        ));
    }

    private function normalizeArtists(Request $request): array
    {
        $artists = [];

        foreach ($request->input('artists', []) as $index => $row) {
            $row = is_array($row) ? $row : [];

            if (empty($row['name'] ?? '') && empty($row['genre'] ?? '')) {
                continue;
            }

            $image = $row['current_image'] ?? '';

            if ($file = $request->file("artists.{$index}.image")) {
                $image = $this->storeImage($file, 'artist');
            }

            $artists[] = [
                'name' => $row['name'] ?? '',
                'genre' => $row['genre'] ?? '',
                'image' => $image,
                'overlay_color' => $row['overlay_color'] ?? '',
            ];
        }

        return $artists;
    }

    private function storeImage($file, string $prefix): string
    {
        $directory = public_path('uploads/homepage');

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $name = $prefix . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $name);

        return 'uploads/homepage/' . $name;
    }
}