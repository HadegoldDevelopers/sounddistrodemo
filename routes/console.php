<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('scan:audio {path} {userId?}', function ($path, $userId = null) {
    $scanner = app(App\Services\AudioScannerService::class);

    if (!$scanner->enabled()) {
        return;
    }

    $result = $scanner->scan($path) ?? [];
    $verdict = $scanner->interpret($result);

    App\Models\CopyrightScan::updateOrCreate(['audio_path' => 'songs/' . basename($path)], [
        'user_id'        => $userId ? (int) $userId : null,
        'status'         => $verdict['status'],
        'matched_title'  => $verdict['matched_title'],
        'matched_artist' => $verdict['matched_artist'],
        'response'       => json_encode($result),
    ]);
})->purpose('Scan a stitched master audio file for copyright matches via ACRCloud');