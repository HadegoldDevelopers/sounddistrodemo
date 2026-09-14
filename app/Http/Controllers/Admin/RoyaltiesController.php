<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatReportService;
use App\Helpers\CountryHelper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\Csv\Reader;
use App\Models\Stat;
use App\Models\UserBalance;
use App\Models\Setting;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Log;

class RoyaltiesController extends Controller
{ 
    protected array $unmatchedArtists = [];

    public function reports(Request $request, StatReportService $service)
{
    $base = $service->applyFilters(Stat::query(), $request);

    $stats = $service->calculateStats($base);

    $payoutToLabels = Withdrawal::where('status', 'paid')
        ->whereHas('user', fn ($q) => $q->where('role', 'label'))
        ->get()
        ->sum(fn ($w) => convertCurrency($w->amount, $w->currency ?? 'USD', 'USD'));

    $payoutToArtists = Withdrawal::where('status', 'paid')
        ->whereHas('user', fn ($q) => $q->where('role', 'artist'))
        ->get()
        ->sum(fn ($w) => convertCurrency($w->amount, $w->currency ?? 'USD', 'USD'));

    // Approved Earnings
    $approvedEarnings = UserBalance::where('status', 'approved')->get()
        ->sum(fn ($b) => convertCurrency($b->amount, $b->currency ?? 'USD', 'USD'));

    return view('admin.royalties.index', compact(
        'stats',
        'payoutToArtists',
        'payoutToLabels',
        'approvedEarnings'
    ));
}


    public function royaltiesReport()
    {
        return view('admin.royalties.reports.royalties');
    }
    

    public function streamsReport(Request $request, StatReportService $service)
{
    $filters = $request->only(['year', 'month', 'country', 'store', 'music_id', 'song', 'artist_id']);

    $report = $service->getStreamsDetailReport($filters, 25);

    $stores = Stat::distinct()->pluck('store');

    // Distinct list of artists for filter dropdown
    $artists = Stat::query()
        ->join('music', 'stats.music_id', '=', 'music.id')
        ->select('stats.artist_id', 'music.artist')
        ->distinct()
        ->orderBy('music.artist')
        ->get();

    foreach ($report['stats'] as $stat) {
        $stat->period = date('F Y', mktime(0, 0, 0, $stat->month, 1, $stat->year));
    }

    $months = [];
    for ($m = 1; $m <= 12; $m++) {
        $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
    }

    return view('admin.royalties.streams', [
        'months'   => $months,
        'stats'    => $report['stats'],
        'summary'  => $report['summary'],
        'stores'   => $stores,
        'countries'=> $report['countries'],
        'artists'  => $artists, // <-- pass distinct artists
    ]);
}


public function streamDetail(Request $request, StatReportService $service)
{
    Log::info('Request data:', $request->all());

    $musicId = $request->music_id;
    $year    = $request->year;
    $month   = $request->month;

    // Get data from service
    $data = $service->buildStreamDetailPageData([
        'music_id' => $musicId,
        'year'     => $year,
        'month'    => $month,
        'store'    => $request->store,
        'country'  => $request->country,
    ]);

    // Log stores and countries (from $data)
    Log::info('Stores:', $data['stores']);
    Log::info('Countries:', $data['countries']);

    // Prepare period for display
    $period = null;

    // Check if year and month are provided and calculate period
    if ($year && $month) {
        $period = date('F Y', mktime(0, 0, 0, $month, 1, $year));
    }

    // Log the calculated period (after it's set)
    Log::info('Calculated Period:', ['period' => $period]);

    // Log the full stream detail data (for debugging purposes)
    Log::info('Stream detail data:', $data);

    // Return view with data and period
    return view('admin.royalties.reports.streams-detail', array_merge($data, [
        'period' => $period,
    ]));
}


    
    public function earningsReport()
    {
        $pending = UserBalance::with('user:id,name', 'music:id,title') ->where('status', 'pending') ->orderBy('created_at', 'desc') ->paginate(100);
        
        return view('admin.royalties.reports.earnings',compact(
            'pending'
            
            ));
    }
    
    public function approveEarnings(UserBalance $balance)
{
    $balance->update([
        'status' => 'approved',
        'updated_at' => now(),
    ]);
    
    $balance->user->increment('wallet_balance', $balance->amount);

    return back()->with('success', 'Earning approved successfully.');
}

public function massApprove(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('error', 'No earnings selected.');
    }

    $earnings = UserBalance::whereIn('id', $ids)
        ->where('status', 'pending')
        ->get();

    foreach ($earnings as $earning) {

        // 1. Update earning status
        $earning->status = 'approved';
        $earning->updated_at = now();
        $earning->save();

        // 2. Update user balance
        $earning->user->increment('wallet_balance', $earning->amount);
    }

    return back()->with('success', 'Selected earnings approved successfully.');
}

public function rejectEarnings(UserBalance $balance)
{
    // Only allow pending earnings to be rejected
    if ($balance->status !== 'pending') {
        
        return back()->with('error', 'Only pending earnings can be rejected.');
    }

    $balance->update([
        'status' => 'rejected',
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Earning rejected successfully.');
}

public function massReject(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('error', 'No earnings selected.');
    }

    $earnings = UserBalance::whereIn('id', $ids)
        ->where('status', 'pending')
        ->get();

    foreach ($earnings as $earning) {
        $earning->status = 'rejected';
        $earning->updated_at = now();
        $earning->save();
    }
    return back()->with('success', 'Selected earnings rejected successfully.');
}

    public function uploadReport(Request $request)
    {
        $request->validate([
            'distributor' => 'required|in:soundrop,auto',
            'earnings_csv' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('earnings_csv')->store('temp', 'private');
        $fullPath = storage_path('app/private/' . $path);

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Uploaded file not found at: ' . $fullPath);
        }

        match ($request->distributor) {
    'soundrop'  => $this->processCsvWithHash($fullPath, fn($p, $h) => $this->parseSoundrop($p, $h)),
    'auto'      => $this->processCsvWithHash($fullPath, fn($p, $h) => $this->parseAuto($p, $h)),
};


        Storage::delete($path);

        $message = 'Royalties report processed.';
        if (!empty($this->unmatchedArtists)) {
            $message .= ' Some artists could not be matched: ' . implode(', ', array_unique($this->unmatchedArtists));
        }

        return redirect()->route('admin.royalties.reports')->with('success', $message);
    }
    
protected function processCsvWithHash(string $path, callable $parser): void
{
    $hash = hash_file('sha256', $path);

    // Prevent duplicate imports
    if (Stat::where('import_hash', $hash)->exists()) {
        session()->flash('error', 'This CSV has already been imported.');
        return;
    }

    // Call the specific parser and pass the hash
    $parser($path, $hash);
}

    protected function parseSoundrop(string $path, ?string $import_hash = null): void
    {
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $row) {
            $artist = trim($row['Artist'] ?? '');
            $track = trim($row['Track Title'] ?? '');
            $amount = trim($row['Amount Due in USD'] ?? 0);
            $streams = intval($row['Quantity'] ?? 0);
            $store = $row['Service'] ?? 'Soundrop';
            $country = $row['Country'] ?? 'Unknown';
            $month = date('m', strtotime($row['Transaction Month'] ?? now()));
            $year = date('Y', strtotime($row['Transaction Month'] ?? now()));
            $isrc = trim($row['ISRC'] ?? null);
            $upc = trim($row['UPC'] ?? null);

            $this->saveStat($artist, $track, $streams, $amount, $store, $country, $year, $month, $isrc, $upc, $import_hash);
        }
    }

protected function parseAuto(string $path, ?string $import_hash = null): void
    {
        $csv = \League\Csv\Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();
    
        foreach ($csv->getRecords() as $row) {
            $normalized = $this->normalizeCsvRow($row, $headers);
            
            if (!$normalized) {
                continue;
            }

            $this->saveStat(
                $normalized['artist'],
                $normalized['track'],
                $normalized['streams'],
                $normalized['amount'],
                $normalized['store'],
                $normalized['country'],
                $normalized['year'],
                $normalized['month'],
                $normalized['isrc'],
                $normalized['upc'],
                $import_hash
            );
        }
    }
    protected function normalizeCsvRow(array $row, array $headers): ?array
    {
        $map = [
            'artist' => ['artist', 'track artist', 'artist name'],
            'track' => ['title', 'track title', 'song title'],
            'isrc' => ['isrc'],
            'upc' => ['upc', 'barcode'],
            'streams' => ['streams', 'play count', 'quantity', 'count'],
            'amount' => ['revenue', 'net revenue', 'label amount (£)', 'amount due in usd', 'royalty', 'royalty ($US)'],
            'store' => ['store', 'platform', 'service'],
            'country' => ['country', 'country of sale', 'territory'],
            'date' => ['date', 'transaction month', 'date of sale', 'reporting period', 'period', 'activity period'],
        ];

        $get = function (array $keys) use ($row) {
            foreach ($keys as $key) {
                foreach ($row as $header => $value) {
                    if (stripos($header, $key) !== false) {
                        return trim($value);
                    }
                }
            }
            return null;
        };

        $artist = $get($map['artist']);
        $track = $get($map['track']);
      
        if (!$artist || !$track) return null;

        $date = $get($map['date']) ?? now();
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));

        return [
            'artist' => $artist,
            'track' => $track,
            'isrc' => $get($map['isrc']),
            'upc' => $get($map['upc']),
            'streams' => intval($get($map['streams']) ?? 0),
            'amount' => floatval($get($map['amount']) ?? 0),
            'store' => $get($map['store']) ?? 'Unknown',
            'country' => CountryHelper::getCountryName($get($map['country']) ?? ''),
            'year' => $year,
            'month' => $month,
        ];
    }


    
protected function saveStat(
    string $artistName,
    string $trackTitle,
    int $streams,
    string $amount,
    string $store,
    string $country,
    string $year,
    string $month,
    ?string $isrc,
    ?string $upc,
    ?string $import_hash
): void

{
    if (!$artistName) {
        return;
    }
    
    $user = match_artist_user($artistName);

    if (!$user) {
        $this->unmatchedArtists[] = "$artistName - $trackTitle";
        return;
    }
    
    $music = null;

    // Priority 1: ISRC
    if ($isrc) {
        $music = $user->music()->where('isrc', $isrc)->first();
    }

    // Priority 2: UPC
    if (!$music && $upc) {
        $music = $user->music()->where('upc', $upc)->first();
    }

    // Priority 3: Exact title
    if (!$music) {
    $cleanTitle = strtolower($trackTitle);
$cleanTitle = preg_replace('/\((.*?)\)/', '', $cleanTitle);
$cleanTitle = preg_replace('/feat\.?.*/i', '', $cleanTitle);
$cleanTitle = trim($cleanTitle);
$music = $user->music()
    ->get()
    ->first(function ($item) use ($cleanTitle) {
        return strtolower($item->title) === $cleanTitle;
    });

    }
    // 3. If still no match → log and skip
if (!$music) {
    $this->unmatchedArtists[] = "$artistName - $trackTitle";
    return;
}

$artistRecord = $user->artist;

// 4. Save the row (raw CSV data)
Stat::create([
    'user_id'     => $user->id,
    'artist_id'   => $artistRecord?->id, 
    'music_id'    => $music->id,
    'year'        => $year,
    'month'       => $month,
    'store'       => $store,
    'country'     => $country,
    'quality'     => 'standard',
    'import_hash' => $import_hash,
    'streams'     => $streams,
    'earnings'    => $amount,
]);

// 5. Save the money into user_balance (pending)
UserBalance::create([
    'user_id'     => $user->id,
    'music_id'    => $music->id,
    'amount'      => $amount,
    'streams'     => $streams,
    'month'       => $month,
    'year'        => $year,
    'status'      => 'pending',
    'import_hash' => $import_hash,
]);

}

}