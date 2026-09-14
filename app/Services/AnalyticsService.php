<?php

namespace App\Services;

use App\Models\Stat;
use Carbon\Carbon;

class AnalyticsService
{
    public function albumUploadsLast12Months(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $data = [];
        $period = new \DatePeriod(
            $start,
            new \DateInterval('P1M'),
            $end->copy()->addMonth()
        );

        foreach ($period as $date) {
            $data[$date->format('M Y')] = 0;
        }

        $rows = Stat::selectRaw('
                year,
                month,
                COUNT(DISTINCT music_id) as total_uploads
            ')
            ->whereRaw(
                "STR_TO_DATE(CONCAT(year, '-', month, '-01'), '%Y-%m-%d') BETWEEN ? AND ?",
                [$start, $end]
            )
            ->groupBy('year', 'month')
            ->get();

        foreach ($rows as $row) {
            $label = Carbon::create($row->year, $row->month)->format('M Y');
            $data[$label] = $row->total_uploads;
        }

        return $data;
    }
}
