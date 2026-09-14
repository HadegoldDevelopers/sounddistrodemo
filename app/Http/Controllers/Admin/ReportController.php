<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Stat;
use App\Models\UserBalance;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // EXPORT — Download CSV
    // GET /admin/royalties/export?artist_id=&year=&month=
    // ─────────────────────────────────────────────────────────────

    public function exportArtistMonthly(Request $request)
    {
        $artistId = $request->input('artist_id');
        $year     = $request->input('year');
        $month    = $request->input('month');

        $rows = $this->getApprovedStatRows($artistId, $year, $month);

        if ($rows->isEmpty()) {
            return response()->streamDownload(function () {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['No approved earnings found for the selected filters.']);
                fclose($out);
            }, 'no-data.csv');
        }

        $siteName   = Setting::getValue('site_name', 'WhiteLabel Distro');
        $artistName = $artistId
            ? ($rows->first()->user?->artist?->name ?? $rows->first()->user?->name ?? 'Artist')
            : null;
        $period     = $this->buildPeriod($year, $month);
        $filename   = $this->buildFilename($siteName, $artistName, $year, $month);

        return response()->streamDownload(
            function () use ($rows, $siteName, $artistName, $period) {
                $out = fopen('php://output', 'w');
                fwrite($out, $this->buildCsvString($rows, $siteName, $artistName, $period));
                fclose($out);
            },
            $filename
        );
    }


    // ─────────────────────────────────────────────────────────────
    // SEND — Email CSV to artist
    // POST /admin/royalties/send-report
    // ─────────────────────────────────────────────────────────────

    public function sendReportToArtist(Request $request)
    {
        $request->validate([
            'artist_id' => 'required|integer',
            'year'      => 'nullable|integer',
            'month'     => 'nullable|integer',
        ]);

        $artistId = $request->input('artist_id');
        $year     = $request->input('year');
        $month    = $request->input('month');

        $rows = $this->getApprovedStatRows($artistId, $year, $month);

        if ($rows->isEmpty()) {
            return back()->with('error', 'No approved earnings found for this artist.');
        }

        $siteName   = Setting::getValue('site_name', 'WhiteLabel Distro');
        $firstRow   = $rows->first();
        $artistName = $firstRow->user?->artist?->name ?? $firstRow->user?->name ?? 'Artist';
        $user       = $firstRow->user;
        $period     = $this->buildPeriod($year, $month);
        $filename   = $this->buildFilename($siteName, $artistName, $year, $month);

        NotificationService::earningsReport(
            user:          $user,
            csvContent:    $this->buildCsvString($rows, $siteName, $artistName, $period),
            filename:      $filename,
            period:        $period,
            totalStreams:  (int) $rows->sum('streams'),
            totalEarnings: (float) $rows->sum('earnings'),
        );

        return back()->with('success', "Earnings report sent to {$user->email} successfully.");
    }


    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Get approved stat rows for an artist/year/month combination.
     * Uses user_balances as the approval whitelist, stats for per-store data.
     */
    private function getApprovedStatRows(?int $artistId, ?int $year, ?int $month)
    {
        $approvedQuery = UserBalance::where('status', 'approved');

        if ($artistId) {
            $approvedQuery->whereHas('user.artist', fn($q) => $q->where('id', $artistId));
        }

        if ($year)  $approvedQuery->where('year',  $year);
        if ($month) $approvedQuery->where('month', $month);

        $approvedKeys = $approvedQuery
            ->select('user_id', 'music_id', 'year', 'month')
            ->get()
            ->map(fn($b) => "{$b->user_id}_{$b->music_id}_{$b->year}_{$b->month}")
            ->toArray();

        if (empty($approvedKeys)) {
            return collect();
        }

        return Stat::with(['user.artist', 'music'])
            ->where(function ($q) use ($approvedKeys) {
                foreach ($approvedKeys as $key) {
                    [$uid, $mid, $yr, $mo] = explode('_', $key);
                    $q->orWhere(function ($inner) use ($uid, $mid, $yr, $mo) {
                        $inner->where('user_id',  $uid)
                              ->where('music_id', $mid)
                              ->where('year',     $yr)
                              ->where('month',    $mo);
                    });
                }
            })
            ->orderBy('year',  'desc')
            ->orderBy('month', 'desc')
            ->orderBy('store')
            ->get();
    }

    /**
     * Build the human-readable period string.
     * e.g. "January 2025", "2025", "All Time"
     */
    private function buildPeriod(?int $year, ?int $month): string
    {
        if ($year && $month) {
            return date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $year;
        }

        return $year ? (string) $year : 'All Time';
    }

    /**
     * Build the CSV filename from site name, artist name, and period filters.
     * e.g. "distrokit-burna-boy-earnings-January-2025.csv"
     */
    private function buildFilename(string $siteName, ?string $artistName, ?int $year, ?int $month): string
    {
        $base = Str::slug($siteName);
        $base .= $artistName ? '-' . Str::slug($artistName) : '-all-artists';
        $base .= '-earnings';

        if ($year && $month) {
            $base .= '-' . date('F', mktime(0, 0, 0, $month, 1)) . '-' . $year;
        } elseif ($year) {
            $base .= '-' . $year;
        }

        return $base . '.csv';
    }

    /**
     * Build the full CSV content as a string.
     * Used by both exportArtistMonthly() and sendReportToArtist().
     */
    private function buildCsvString($rows, string $siteName, ?string $artistName, string $period): string
    {
        $lines = [];

        // Title block
        $lines[] = $this->csvCell("{$siteName} — Earnings Report");
        if ($artistName) {
            $lines[] = $this->csvCell("Artist: {$artistName}");
        }
        $lines[] = $this->csvCell("Period: {$period}");
        $lines[] = $this->csvCell('Generated: ' . now()->format('F j, Y \a\t g:i A'));
        $lines[] = '';

        // Column headers
        $lines[] = implode(',', [
            'Artist', 'Song Title', 'Store', 'Country',
            'Year', 'Month', 'Streams', 'Earnings (USD)',
        ]);

        // Data rows
        foreach ($rows as $row) {
            $lines[] = implode(',', array_map(
                fn($v) => $this->csvCell($v),
                [
                    $row->user?->artist?->name ?? $row->user?->name ?? 'Unknown',
                    $row->music?->title ?? 'Unknown',
                    $row->store   ?? 'Unknown',
                    $row->country ?? 'Unknown',
                    $row->year,
                    date('F', mktime(0, 0, 0, $row->month, 1)),
                    $row->streams ?? 0,
                    number_format($row->earnings, 2),
                ]
            ));
        }

        // Totals
        $lines[] = '';
        $lines[] = implode(',', [
            $this->csvCell('TOTAL'), '""', '""', '""', '""', '""',
            $this->csvCell($rows->sum('streams')),
            $this->csvCell(number_format($rows->sum('earnings'), 2)),
        ]);

        return implode("\n", $lines);
    }

    /**
     * Wrap a value in CSV-safe double quotes, escaping internal quotes.
     */
    private function csvCell(mixed $value): string
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }
}