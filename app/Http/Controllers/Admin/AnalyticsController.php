<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Music;
use App\Models\Stat;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function dashboard()
{
    // Total streams
    $totalStreams = Stat::sum('streams');

    // Streams by store
    $streamsByStore = Stat::selectRaw('store, SUM(streams) as total')
        ->groupBy('store')
        ->orderByDesc('total')
        ->get();

    // Streams by country
    $streamsByCountry = Stat::selectRaw('country, SUM(streams) as total')
        ->groupBy('country')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

    // Top tracks
    $topTracks = Stat::selectRaw('music_id, SUM(streams) as total')
        ->with('music')
        ->groupBy('music_id')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

    // Monthly trend (last 12 months)
    $monthlyTrend = Stat::selectRaw('year, month, SUM(streams) as total')
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

    return view('admin.analytics.index', compact(
        'totalStreams',
        'streamsByStore',
        'streamsByCountry',
        'topTracks',
        'monthlyTrend'
    ));
}

    public function manualEntryForm()
    {
        $tracks = Music::with('artistModel')
    ->where('status', 'approved')
    ->orderBy('title')
    ->get();
    
    $startDate = now()->subDays(7)->toDateString();
    $endDate = now()->toDateString();


        return view('admin.analytics.entry', [
    'tracks' => $tracks,
    'startDate' => $startDate,
    'endDate' => $endDate,
    
    ]);
}

    public function storeManual(Request $request)
{
    $request->validate([
        'music_id'    => 'required|exists:music,id',
        'year'        => 'required|integer|min:2000|max:2100',
        'month'       => 'required|integer|min:1|max:12',
        'country'     => 'nullable|string|max:100',
        'stores'      => 'required|array',
        'stores.*'    => 'nullable|string|max:100',
        'streams'     => 'required|array',
        'streams.*'   => 'nullable|integer|min:0',
    ]);

    $music   = Music::findOrFail($request->music_id);
    $country = $request->country ?: 'Global';
    $saved   = 0;

    foreach ($request->stores as $index => $store) {
        $streams  = (int)   ($request->streams[$index]  ?? 0);
        if (!$store || ($streams === 0 )) {
            continue;
        }

        $artistId = $music->artistModel?->id ?? null;

        Stat::create([
            'user_id'     => $music->user_id,
            'artist_id'   => $artistId,
            'music_id'    => $music->id,
            'store'       => $store,
            'country'     => $country,
            'quality'     => 'standard',
            'streams'     => $streams,
            'year'        => $request->year,
            'month'       => $request->month,
            'import_hash' => 'manual-' . uniqid(),
        ]);

        $saved++;
    }

    if ($saved === 0) {
        return back()->with('error', 'No data was saved. Please enter streams for at least one store.');
    }

    return back()->with('success', "Stats saved for {$saved} " . Str::plural('store', $saved) . '.');
}
}
