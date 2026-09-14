<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

/**
 * ACRCloud copyright scanner.
 *
 * Sends a short snippet of a stitched master audio file to ACRCloud's
 * audio recognition API and returns the raw JSON match response.
 *
 * Configure via .env (leave empty to disable scanning entirely):
 *
 *   ACR_PROJECT_KEY=<access_key>
 *   ACR_PROJECT_SECRET=<access_secret>
 *   ACR_HOST=https://identify-eu-west-1.acrcloud.com
 *
 * IMPORTANT: ACRCloud assigns each project a region-specific host
 * shown in your project console (Projects → identification). Use that
 * exact host here. Examples: identify-eu-west-1.acrcloud.com,
 * identify-ap-southeast-1.acrcloud.com, identify-cn-north-1.acrcloud.com
 *
 * CodeCanyon Buyer: ACRCloud provides a free developer tier (100
 * identifications / month). See their native PHP SDK on GitHub if you
 * prefer $acrcloud->identifyByFile($filePath) over this HTTP client.
 */
class AudioScannerService
{
    /**
     * The ACRCloud access key.
     *
     * @var string
     */
    protected $accessKey;

    /**
     * The ACRCloud access secret.
     *
     * @var string
     */
    protected $accessSecret;

    /**
     * The ACRCloud identify endpoint host.
     *
     * @var string
     */
    protected $host;

    /**
     * Create a new scanner service instance.
     */
    public function __construct()
    {
        $this->accessKey = env('ACR_PROJECT_KEY', '');
        $this->accessSecret = env('ACR_PROJECT_SECRET', '');
        $this->host = rtrim(env('ACR_HOST', 'https://identify-eu-west-1.acrcloud.com'), '/');
    }

    /**
     * Determine if the scanner is configured and ready to use.
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->accessKey !== '' && $this->accessSecret !== '' && $this->host !== '';
    }

    /**
     * Scan an audio file for a copyright match.
     *
     * @param  string  $filePath  Absolute path to the WAV/audio master.
     * @param  int     $seconds   Snippet length in seconds (default 15).
     * @return array|null  Decoded ACRCloud JSON response, or null when disabled/failed.
     */
    public function scan(string $filePath, int $seconds = 15): ?array
    {
        if (!$this->enabled()) {
            return null;
        }

        $snippet = $this->prepareSnippet($filePath, $seconds);
        $timestamp = (string) time();

        $response = Http::timeout(30)
            ->asMultipart()
            ->attach('sample', file_get_contents($snippet), basename($snippet))
            ->post($this->host . '/v1/identify', [
                'access_key'        => $this->accessKey,
                'sample_bytes'      => filesize($snippet),
                'data_type'         => 'audio',
                'signature_version' => '1',
                'timestamp'         => $timestamp,
                'signature'         => $this->signature($timestamp),
            ]);

        if ($snippet !== $filePath) {
            @unlink($snippet);
        }

        return $response->json();
    }

    /**
     * Translate a raw ACRCloud response into a scan verdict.
     *
     * Only "Success" (code 0) and the explicit no-result codes (1001/2001)
     * are treated as valid scans. Anything else (too large, no fingerprint,
     * auth failure…) is recorded as "error" so it is never mistaken for a
     * clean track.
     *
     * @param  array  $result  Decoded ACRCloud JSON.
     * @return array  ['status' => 'blocked'|'clean'|'error', 'matched_title', 'matched_artist']
     */
    public function interpret(array $result): array
    {
        $code = $result['status']['code'] ?? null;
        $matches = $result['metadata']['music'] ?? [];

        if ($code === 0) {
            $status = empty($matches) ? 'clean' : 'blocked';
        } elseif (in_array($code, [1001, 2001])) {
            $status = 'clean';
        } else {
            $status = 'error';
        }

        return [
            'status'         => $status,
            'matched_title'  => $matches[0]['title'] ?? null,
            'matched_artist' => collect($matches[0]['artists'] ?? [])
                ->map(fn($artist) => $artist['name'])
                ->implode(', '),
        ];
    }

    /**
     * Extract a short mono WAV snippet with FFmpeg when available, or by
     * slicing the raw WAV payload directly when not. Falls back to the
     * original file so scanning never blocks on tooling.
     *
     * @param  string  $filePath
     * @param  int     $seconds
     * @return string  Path to the snippet (or the original file when unsupported).
     */
    protected function prepareSnippet(string $filePath, int $seconds): string
    {
        $ffmpeg = Process::command(['which', 'ffmpeg'])->run();

        if ($ffmpeg->successful()) {
            $snippet = $this->tempPath() . '/acr-' . uniqid() . '.wav';

            $result = Process::command([
                'ffmpeg', '-y',
                '-ss', '0',
                '-t', (string) $seconds,
                '-i', $filePath,
                '-acodec', 'pcm_s16le',
                '-ar', '44100',
                '-ac', '1',
                $snippet,
            ])->run();

            if ($result->successful()) {
                return $snippet;
            }
        }

        if (strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'wav') {
            $snippet = $this->extractWavSnippet($filePath, $seconds);

            if ($snippet) {
                return $snippet;
            }
        }

        // Compressed formats (MP3, M4A, FLAC…): send a small head-slice so
        // ACRCloud never rejects the request as "too large". ACRCloud only
        // needs 10-20 seconds of audio to identify a track.
        if (filesize($filePath) > 1048576) {
            $snippet = $this->headSlice($filePath, 1048576);

            if ($snippet) {
                return $snippet;
            }
        }

        return $filePath;
    }

    /**
     * Write the first (maxBytes) of any file to a temp snippet.
     * Decoders ignore a truncated tail, so this yields a playable clip
     * without needing FFmpeg.
     *
     * @param  string  $filePath
     * @param  int     $maxBytes
     * @return string|null
     */
    protected function headSlice(string $filePath, int $maxBytes): ?string
    {
        $handle = fopen($filePath, 'rb');

        if (!$handle) {
            return null;
        }

        try {
            $data = fread($handle, $maxBytes);
        } finally {
            fclose($handle);
        }

        if (strlen($data) <= 0) {
            return null;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $path = $this->tempPath() . '/acr-' . uniqid() . '.' . $extension;

        file_put_contents($path, $data);

        return $path;
    }

    /**
     * Slice the first (n) seconds out of a WAV file in pure PHP.
     * Only useful for raw PCM WAV files (the format produced by the
     * chunk stitcher), keeping requests small (< 1.5MB for 15s stereo).
     *
     * @param  string  $filePath
     * @param  int     $seconds
     * @return string|null
     */
    protected function extractWavSnippet(string $filePath, int $seconds): ?string
    {
        $handle = fopen($filePath, 'rb');

        if (!$handle) {
            return null;
        }

        try {
            $header = fread($handle, 44);

            if (substr($header, 0, 4) !== 'RIFF' || substr($header, 8, 4) !== 'WAVE') {
                return null;
            }

            $channels     = unpack('v', substr($header, 22, 2))[1];
            $sampleRate   = unpack('V', substr($header, 24, 4))[1];
            $bitsPerSample = unpack('v', substr($header, 34, 2))[1];

            if ($channels <= 0 || $sampleRate <= 0 || $bitsPerSample <= 0) {
                return null;
            }

            $bytesPerSecond = $channels * $sampleRate * ($bitsPerSample / 8);

            // Walk the chunk list to locate the data payload.
            $offset = 12;
            $dataOffset = null;

            while ($offset + 8 <= filesize($filePath)) {
                fseek($handle, $offset);
                $chunkHeader = fread($handle, 8);
                $chunkSize = unpack('V', substr($chunkHeader, 4, 4))[1];

                if (substr($chunkHeader, 0, 4) === 'data') {
                    $dataOffset = $offset + 8;
                    break;
                }

                $offset += 8 + $chunkSize + ($chunkSize % 2);
            }

            if ($dataOffset === null) {
                return null;
            }

            $snippetBytes = min(filesize($filePath) - $dataOffset, (int) ($seconds * $bytesPerSecond));

            if ($snippetBytes <= 0) {
                return null;
            }

            fseek($handle, $dataOffset);
            $data = fread($handle, $snippetBytes);

            // Rebuild a canonical header for the sliced data.
            $snippetHeader =
                'RIFF' . pack('V', 36 + strlen($data)) . 'WAVE' .
                'fmt ' . pack('V', 16) . substr($header, 20, 16) .
                'data' . pack('V', strlen($data));

            $snippetPath = $this->tempPath() . '/acr-' . uniqid() . '.wav';
            file_put_contents($snippetPath, $snippetHeader . $data);

            return $snippetPath;
        } finally {
            fclose($handle);
        }
    }

    /**
     * Ensure the temp directory exists and return its path.
     *
     * @return string
     */
    protected function tempPath(): string
    {
        $directory = storage_path('temp');

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        return $directory;
    }

    /**
     * Build the HMAC-SHA1 request signature required by ACRCloud.
     *
     * @param  string  $timestamp
     * @return string
     */
    protected function signature(string $timestamp): string
    {
        $stringToSign = implode("\n", [
            'POST',
            '/v1/identify',
            $this->accessKey,
            'audio',
            '1',
            $timestamp,
        ]);

        return base64_encode(hash_hmac('sha1', $stringToSign, $this->accessSecret, true));
    }
}