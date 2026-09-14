<?php

namespace App\Services;

use App\Models\Stat;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\CountryHelper;
use Illuminate\Support\Facades\Log;


class StatReportService
{
  

  protected array $months = [
    'January' => 1,
    'February' => 2,
    'March' => 3,
    'April' => 4,
    'May' => 5,
    'June' => 6,
    'July' => 7,
    'August' => 8,
    'September' => 9,
    'October' => 10,
    'November' => 11,
    'December' => 12
  ];

public function processCountry($countryCode)
{
    // Use the helper to normalize and map the country
    $countryName = CountryHelper::normalizeCountryCode($countryCode); 

    // You can now use the normalized country name, no need for extra logic
    return $countryName ?? 'Unknown';
}

  

  /**
   * Apply all filters to the Stat query.
   */
  public function applyFilters($query, Request $request)
  {
    return $query
      ->when(
        $request->input('range') === 'today',
        fn($q) =>
        $q->whereDate('created_at', today())
      )
      ->when(
        $request->input('range') === 'month',
        fn($q) =>
        $q->whereYear('created_at', now()->year)
          ->whereMonth('created_at', now()->month)
      )
      ->when(
        $request->filled('from'),
        fn($q) =>
        $q->whereDate('created_at', '>=', $request->from)
      )
      ->when(
        $request->filled('to'),
        fn($q) =>
        $q->whereDate('created_at', '<=', $request->to)
      )
      ->when(
        $request->filled('country'),
        fn($q) =>
        $q->where('country', $request->country)
      )
      ->when(
        $request->filled('music_id'),
        fn($q) =>
        $q->where('music_id', $request->music_id)
      )
      ->when(
        $request->filled('user_id'),
        fn($q) =>
        $q->where('user_id', $request->user_id)
      )
      ->when(
        $request->filled('role'),
        fn($q) =>
        $q->whereHas('user', fn($x) => $x->where('role', $request->role))
      )
      ->when(
        $request->filled('platform'),
        fn($q) =>
        $q->where('platform', $request->platform)
      );
  }

  /**
   * Calculate stats based on the filtered query.
   */
  public function calculateStats($base)
  {
    return [
      'total_earnings' => (clone $base)->sum('earnings'),
      'total_streams'  => (clone $base)->sum('streams'),
      'artist_payout'  => (clone $base)->whereHas('user', fn($q) => $q->where('role', 'artist'))->sum('earnings'),
      'label_payout'   => (clone $base)->whereHas('user', fn($q) => $q->where('role', 'label'))->sum('earnings'),
      'track_count'    => (clone $base)->distinct('music_id')->count('music_id'),
      'countries'      => (clone $base)->distinct('country')->count('country'),
    ];
  }

  /**
   * Normalize country name
   */
  public function normalizeCountry(?string $country): string
  {
    if (!$country) {
      return 'Unknown'; 
    }

    $key = strtolower($country);
    return CountryHelper::normalizeCountryCode(strtoupper($country)) ?? ucfirst($country);
  }


  public function getStreamsDetailReport(array $filters = [], int $perPage = 25): array
{
    $query = Stat::with('music:id,title,artist');

    // Apply filters
    if (!empty($filters['year'])) $query->where('year', $filters['year']);
    if (!empty($filters['month'])) $query->where('month', $filters['month']);
    if (!empty($filters['store'])) $query->where('store', $filters['store']);
    if (!empty($filters['music_id'])) $query->where('music_id', $filters['music_id']);
    if (!empty($filters['song'])) {
        $song = trim($filters['song']);
        $query->whereHas('music', fn($q) => $q->where('title', 'LIKE', "%$song%"));
    }
    if (!empty($filters['country'])) {
        $keys = $this->getCountryFilterKeys($filters['country']);
        if (empty($keys)) {
            $query->whereRaw('1 = 0');
        } else {
            $query->where(function ($q) use ($keys) {
                foreach ($keys as $key) {
                    $q->orWhereRaw('LOWER(country) = ?', [$key]);
                }
            });
        }
    }

    // Flat aggregation: each row = one artist + song + store + country
    $stats = Stat::query()
    ->join('music', 'stats.music_id', '=', 'music.id')
    ->when(!empty($filters['artist_id']), fn($q) => $q->where('stats.artist_id', $filters['artist_id']))
    ->when(!empty($filters['song']), fn($q) =>
        $q->where('music.title', 'LIKE', '%' . trim($filters['song']) . '%')
    )
    ->when(!empty($filters['year']), fn($q) => $q->where('stats.year', $filters['year']))
    ->when(!empty($filters['month']), fn($q) => $q->where('stats.month', $filters['month']))
    ->when(!empty($filters['store']), fn($q) => $q->where('stats.store', $filters['store']))
    ->when(!empty($filters['country']), function ($q) use ($filters) {
        $keys = $this->getCountryFilterKeys($filters['country']);
        if (!empty($keys)) {
            $q->where(function ($q2) use ($keys) {
                foreach ($keys as $key) {
                    $q2->orWhereRaw('LOWER(stats.country) = ?', [$key]);
                }
            });
        }
    })
    ->selectRaw('
        stats.artist_id as artist_id,
        stats.music_id,
        music.title,
        music.artist,
        stats.year,
        stats.month,
        SUM(stats.streams) as total_streams,
        SUM(stats.earnings) as total_earnings
    ')
    ->groupBy('stats.artist_id', 'stats.music_id', 'music.title', 'music.artist', 'stats.year', 'stats.month')
    ->orderBy('stats.year', 'desc')
    ->orderBy('stats.month', 'desc')
    ->paginate($perPage);

    // Summary
    $allCountries = Stat::pluck('country')
        ->map(fn($c) => trim($this->normalizeCountry($c)))
        ->unique()->sort()->values();

    $summary = [
        'total_streams' => (clone $query)->sum('streams'),
        'total_earnings' => (clone $query)->sum('earnings'),
        'track_count' => (clone $query)->distinct('music_id')->count('music_id'),
        'countries' => $allCountries->count(),
    ];

    // Normalize country names
    $stats->getCollection()->transform(function ($row) {
        $row->country = $this->normalizeCountry($row->country);
        return $row;
    });

    return [
        'stats' => $stats,
        'summary' => $summary,
        'countries' => $allCountries,
    ];
}

  /**
   * Get detailed streams per song per month by store and country.
   * Returns plain array of objects, no Collection used.
   */
  public function getStreamDetailBySong(array $filters = []): array
  {
    $query = Stat::query()->with('music:id,title,artist');

    if (!empty($filters['music_id'])) {
      $query->where('music_id', $filters['music_id']);
    }
    if (!empty($filters['year'])) {
      $query->where('year', $filters['year']);
    }
    if (!empty($filters['month'])) {
      $query->where('month', $filters['month']);
    }
    if (!empty($filters['store'])) {
      $query->where('store', $filters['store']);
    }
    if (!empty($filters['country'])) {
      $keys = $this->getCountryFilterKeys($filters['country']);
      if (!empty($keys)) {
        $query->where(function ($q) use ($keys) {
          foreach ($keys as $key) {
            $q->orWhereRaw('LOWER(country) = ?', [$key]);
          }
        });
      }
    }

    $results = $query
      ->join('music', 'stats.music_id', '=', 'music.id')
      ->selectRaw('
            music.id as music_id,
            music.title,
            music.artist,
            stats.store,
            stats.country,
            SUM(stats.streams) as total_streams,
            SUM(stats.earnings) as total_earnings
        ')
      ->groupBy('music.id', 'music.title', 'music.artist', 'stats.store', 'stats.country')
      ->orderBy('stats.store')
      ->get()
      ->all(); // convert Collection to plain array

    // Normalize country names
    foreach ($results as &$row) {
      $row->country = $this->normalizeCountry($row->country);
    }

    return $results;
  }

  /**
   * Build all data needed for the stream detail page.
   * Returns plain arrays only.
   */
  public function buildStreamDetailPageData(array $filters): array
{
    $musicId = $filters['music_id'];
    $year    = $filters['year'] ?? null;
    $month   = $filters['month'] ?? null;

    // 1. Fetch music info
    $music = \App\Models\Music::findOrFail($musicId);

    // 2. Prepare full dropdowns (all stores and countries for this music track)
    $allStores = Stat::query()
        ->where('music_id', $musicId)
        ->distinct()
        ->orderBy('store')
        ->pluck('store')
        ->all();

    $allCountriesRaw = Stat::query()
        ->where('music_id', $musicId)
        ->distinct()
        ->pluck('country')
        ->all();

    $allCountries = [];
    foreach ($allCountriesRaw as $country) {
        $normalized = $this->normalizeCountry($country);
        if (!in_array($normalized, $allCountries)) {
            $allCountries[] = $normalized;
        }
    }
    sort($allCountries, SORT_STRING);

    // 3. Fetch filtered stats
    $statsQuery = Stat::query()->with('music:id,title,artist')
        ->where('music_id', $musicId);

    if (!empty($filters['store'])) {
        $statsQuery->where('store', $filters['store']);
    }
    if (!empty($filters['country'])) {
        $normalizedCountry = $this->normalizeCountry($filters['country']);
        $statsQuery->whereRaw('LOWER(country) = ?', [strtolower($normalizedCountry)]);
    }
    if (!empty($year)) {
        $statsQuery->where('year', $year);
    }
    if (!empty($month)) {
        $statsQuery->where('month', $month);
    }

    $stats = $statsQuery
        ->selectRaw('
    stats.artist_id as artist_id,
    stats.music_id,
    stats.store,
    stats.country,
    stats.year,
    stats.month,
    SUM(stats.streams) as total_streams,
    SUM(stats.earnings) as total_earnings
')
->groupBy(
    'stats.artist_id',
    'stats.music_id',
    'stats.store',
    'stats.country',
    'stats.year',
    'stats.month'
)->orderBy('store')
        ->get()
        ->map(function ($row) {
            $row->country = $this->normalizeCountry($row->country);
            return $row;
        })
        ->all();

    // 4. Calculate the period (Month and Year)
    $period = null;

    // Check if both year and month are set
    if ($year && $month) {
        // Format period as "Month Year"
        $period = \Carbon\Carbon::create($year, $month, 1)->format('F Y');
    } elseif ($year) {
        // Only year available
        $period = $year;
    } elseif ($month) {
        // Only month available
        $period = \Carbon\Carbon::create()->month($month)->format('F');
    }

    // Return the data
    return [
        'music'     => $music,
        'year'      => $year,
        'month'     => $month,
        'period'    => $period,   // Passing the period to the view
        'stats'     => $stats,
        'stores'    => $allStores,
        'countries' => $allCountries
    ];
}




 public function getAllArtistsMonthlyReport(array $filters = []): array
{
    $query = Stat::query()
        ->join('music', 'stats.music_id', '=', 'music.id')
        ->selectRaw('
            stats.artist_id as artist_id,
            music.artist,
            music.title,
            stats.year,
            stats.month,
            stats.store,
            stats.country,
            SUM(stats.streams) as total_streams,
            SUM(stats.earnings) as total_earnings
        ')
        ->groupBy(
            'stats.artist_id',
            'music.artist',
            'music.title',
            'stats.year',
            'stats.month',
            'stats.store',
            'stats.country'
        );

    if (!empty($filters['year'])) {
        $query->where('stats.year', $filters['year']);
    }
    if (!empty($filters['month'])) {
        $query->where('stats.month', $filters['month']);
    }
    if (!empty($filters['store'])) {
        $query->where('stats.store', $filters['store']);
    }
    if (!empty($filters['country'])) {
        $keys = $this->getCountryFilterKeys($filters['country']);
        if (!empty($keys)) {
            $query->where(function ($q) use ($keys) {
                foreach ($keys as $key) {
                    $q->orWhereRaw('LOWER(stats.country) = ?', [$key]);
                }
            });
        }
    }

    return $query->orderBy('music.artist')->get()->toArray();
}



  // In App\Services\StatReportService.php
  public function exportArtistMonthlyForArtist(int $artistId, array $filters = []): array
{
    $query = Stat::query()
        ->join('music', 'stats.music_id', '=', 'music.id')
        ->where('stats.artist_id', $artistId);

    if (!empty($filters['year'])) {
        $query->where('stats.year', $filters['year']);
    }
    if (!empty($filters['month'])) {
        $query->where('stats.month', $filters['month']);
    }
    if (!empty($filters['store'])) {
        $query->where('stats.store', $filters['store']);
    }
    if (!empty($filters['country'])) {
        $keys = $this->getCountryFilterKeys($filters['country']);
        if (!empty($keys)) {
            $query->where(function ($q) use ($keys) {
                foreach ($keys as $key) {
                    $q->orWhereRaw('LOWER(stats.country) = ?', [$key]);
                }
            });
        }
    }

    return $query
        ->selectRaw('
            stats.artist_id as artist_id,
            music.id as music_id,
            music.title,
            music.artist,
            stats.store,
            stats.country,
            stats.year,
            stats.month,
            SUM(stats.streams) as total_streams,
            SUM(stats.earnings) as total_earnings
        ')
        ->groupBy(
            'stats.artist_id',
            'music.id',
            'music.title',
            'music.artist',
            'stats.store',
            'stats.country',
            'stats.year',
            'stats.month'
        )
        ->get()
        ->toArray();
}


}