<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\Project;
use App\Models\Stat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
  public function index(Request $request)
{
    $user = Auth::user();

    $projects = Stat::select(
            'music.project_id',
            DB::raw('SUM(stats.streams) as total_streams'),
            DB::raw('COUNT(DISTINCT stats.music_id) as track_count'),
            DB::raw('COUNT(DISTINCT stats.store) as store_count')
        )
        ->join('music', 'music.id', '=', 'stats.music_id')
        ->join('projects', 'projects.id', '=', 'music.project_id')
        ->where('stats.user_id', $user->id)
        ->where('music.status', 'approved')
        ->whereNotNull('music.project_id')
        ->groupBy('music.project_id')
        ->orderByDesc('total_streams')
        ->paginate(10)
        ->appends($request->query());

    // Attach project model after paginate
    $projects->getCollection()->transform(function ($row) {
        $row->project = Project::find($row->project_id);
        return $row;
    });

    $totalStreams = Stat::join('music', 'music.id', '=', 'stats.music_id')
        ->where('stats.user_id', $user->id)
        ->where('music.status', 'approved')
        ->sum('stats.streams');

    return view('user.analytics.index', compact('user', 'projects', 'totalStreams'));
}


public function release(Request $request, Project $project)
{
    $user = Auth::user();
    
    if ($project->user_id !== $user->id) {
        abort(403);
    }

    $tracks = Stat::select(
            'stats.music_id',
            DB::raw('SUM(stats.streams) as total_streams'),
            DB::raw('COUNT(DISTINCT stats.store) as store_count')
        )
        ->join('music', 'music.id', '=', 'stats.music_id')
        ->where('stats.user_id', $user->id)
        ->where('music.project_id', $project->id)
        ->with('music')
        ->groupBy('stats.music_id')
        ->orderBy('music.track_number')
        ->get();

    $totalStreams = $tracks->sum('total_streams');

    return view('user.analytics.release', compact('user', 'project', 'tracks', 'totalStreams'));
}


    public function show(Request $request, Music $music)
    {
        $user = Auth::user();

        if ($music->user_id !== $user->id) {
            abort(403);
        }

        // ── Date range filter ─────────────────────────────────────
        $query = Stat::where('music_id', $music->id)
                     ->where('user_id', $user->id);

        $periodLabel = 'All Time';
        $range       = $request->input('range');

        if ($range) {
            [$startYear, $startMonth, $endYear, $endMonth, $periodLabel] = $this->resolveRange($range);

            $query->where(function ($q) use ($startYear, $startMonth, $endYear, $endMonth) {
                $q->whereRaw('(year > ? OR (year = ? AND month >= ?))', [$startYear, $startYear, $startMonth])
                  ->whereRaw('(year < ? OR (year = ? AND month <= ?))', [$endYear,   $endYear,   $endMonth]);
            });
        }

        if ($request->filled('store')) {
            $query->where('store', $request->store);
        }

        $stats = $query->get();

        // ── Store breakdown ───────────────────────────────────────
        $storeBreakdown = $stats
            ->groupBy('store')
            ->map(fn($s) => $s->sum('streams'))
            ->sortByDesc(fn($v) => $v);

        // ── Country breakdown (DB grouped, paginated) ─────────────────
$countryBreakdown = Stat::select('country', DB::raw('SUM(streams) as streams'))
    ->where('music_id', $music->id)
    ->where('user_id', $user->id)
   ->when($range, function ($q) use ($range) {
    [$startYear, $startMonth, $endYear, $endMonth] = $this->resolveRange($range);

    $q->where(function ($sub) use ($startYear, $startMonth, $endYear, $endMonth) {
        $sub->whereRaw('(year > ? OR (year = ? AND month >= ?))', [$startYear, $startYear, $startMonth])
            ->whereRaw('(year < ? OR (year = ? AND month <= ?))', [$endYear,   $endYear,   $endMonth]);
    });
})
    ->when($request->filled('store'), fn($q) => $q->where('store', $request->store))
    ->groupBy('country')
    ->orderByDesc('streams')
    ->paginate($request->input('countries_per_page', 10))
    ->appends($request->query());


        // ── Monthly trend ─────────────────────────────────────────
        $monthlyTrend = $stats
            ->groupBy(fn($s) => $s->year . '-' . str_pad($s->month, 2, '0', STR_PAD_LEFT))
            ->map(fn($s) => $s->sum('streams'))
            ->sortKeys();

        // ── Totals ────────────────────────────────────────────────
        $totalStreams = $stats->sum('streams');

        // ── Stores for filter ─────────────────────────────────────
        $stores = Stat::where('music_id', $music->id)
            ->distinct()->pluck('store')->sort()->values();

        return view('user.analytics.stats', compact(
            'user',
            'music',
            'storeBreakdown',
            'countryBreakdown',
            'monthlyTrend',
            'totalStreams',
            'stores',
            'periodLabel'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // Resolve range string to start/end year+month
    // ─────────────────────────────────────────────────────────────
    private function resolveRange(string $range): array
{
    $now      = Carbon::now();
    $endYear  = (int) $now->format('Y');
    $endMonth = (int) $now->format('m');

    switch ($range) {

        // ── Day-based ranges ───────────────────────────────
        case '7d':
            $start       = $now->copy()->subDays(7);
            $periodLabel = 'Last 7 Days';
            break;

        case '14d':
            $start       = $now->copy()->subDays(14);
            $periodLabel = 'Last 14 Days';
            break;

        case '30d':
            $start       = $now->copy()->subDays(30);
            $periodLabel = 'Last 30 Days';
            break;

        case '90d':
            $start       = $now->copy()->subDays(90);
            $periodLabel = 'Last 90 Days';
            break;

        // ── Month-based ranges ─────────────────────────────
        case '1m':
            $start       = $now->copy()->subMonth();
            $periodLabel = 'Last 30 Days';
            break;

        case '3m':
            $start       = $now->copy()->subMonths(3);
            $periodLabel = 'Last 3 Months';
            break;

        case '6m':
            $start       = $now->copy()->subMonths(6);
            $periodLabel = 'Last 6 Months';
            break;

        case '12m':
            $start       = $now->copy()->subMonths(12);
            $periodLabel = 'Last 12 Months';
            break;

        case 'ytd':
            $start       = Carbon::create($now->year, 1, 1);
            $periodLabel = 'This Year (' . $now->year . ')';
            break;

        default:
            $start       = $now->copy()->subMonth();
            $periodLabel = 'Last 30 Days';
    }

    return [
        (int) $start->format('Y'),
        (int) $start->format('m'),
        $endYear,
        $endMonth,
        $periodLabel,
    ];
}

}