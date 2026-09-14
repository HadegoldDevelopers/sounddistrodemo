<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\CopyrightScan;
use App\Models\Music;
use App\Models\Project;
use App\Services\AudioScannerService;
use App\Services\NotificationService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MusicController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $artists = [];

        if ($user->role === 'artist') {
            $artist = $user->artist;

            if (!$artist || !$artist->isProfileComplete()) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Please complete your artist profile before uploading.');
            }
        } elseif ($user->role === 'label' && $user->label) {
            $artists = $user->label->artist;
        }

        return view('music.upload', [
            'user'    => $user,
            'artists' => $artists,
        ]);
    }

    public function uploadCover(Request $request)
    {
        $request->validate([
            'cover' => 'required|image|max:10000',
        ]);

        $file = $request->file('cover');

        // Generate a secure, clean filename
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(16) . '.' . $extension;

        // Ensure directory exists
        $directory = public_path('temp/covers');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Move file
        $file->move($directory, $filename);

        return response()->json([
            'path' => "temp/covers/$filename",
            'url'  => assetPath("temp/covers/$filename"),
        ]);
    }


    // CHUNKED AUDIO UPLOAD
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'project_title' => 'required|string',
            'track_index'   => 'required|integer',
            'chunk'         => 'required|file',
            'chunk_number'  => 'required|integer',
            'total_chunks'  => 'required|integer',
            'original_name' => 'required|string',
        ]);

        $projectTitle = $request->project_title;
        $trackIndex   = (int) $request->track_index;
        $chunkNumber  = (int) $request->chunk_number;
        $totalChunks  = (int) $request->total_chunks;
        $originalName = $request->original_name;

        $safeTitle = preg_replace('/\s+/', '_', trim($projectTitle));
        $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '', $safeTitle);

        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $trackNumber = str_pad($trackIndex, 2, '0', STR_PAD_LEFT);

        $tempDir = public_path('temp/chunks');
        if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);

        $chunkFile = $request->file('chunk');
        $chunkPath = $tempDir . '/' . $safeTitle . '_Track_' . $trackNumber . '.part' . $chunkNumber;

        $chunkFile->move($tempDir, basename($chunkPath));

        // If last chunk, assemble
        if ($chunkNumber == $totalChunks) {
            $finalDir = public_path('songs');
            if (!is_dir($finalDir)) mkdir($finalDir, 0777, true);

            $finalName = $safeTitle . '_Track_' . $trackNumber . '.' . $ext;
            $finalPath = $finalDir . '/' . $finalName;

            $out = fopen($finalPath, 'wb');

            for ($i = 1; $i <= $totalChunks; $i++) {
                $partPath = $tempDir . '/' . $safeTitle . '_Track_' . $trackNumber . '.part' . $i;
                if (!file_exists($partPath)) continue;

                $in = fopen($partPath, 'rb');
                while ($buff = fread($in, 1048576)) fwrite($out, $buff);
                fclose($in);
                unlink($partPath);
            }

            fclose($out);

            // Route the finished master through the copyright scanner. The
            // snippet is small (≤1MB), so this returns in a couple of
            // seconds and guarantees the verdict is recorded before the
            // release can be submitted. Non-fatal on scanner failure.
            $this->inspectMasterFile($finalPath);

            return response()->json([
                'done' => true,
                'path' => 'songs/' . $finalName,
                'url'  => asset('songs/' . $finalName),
            ]);
        }

        return response()->json(['done' => false]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title'        => 'required',
            'artist'       => $user->role === 'artist' ? 'required' : 'nullable',
            'artist_id'    => $user->role === 'label' ? 'required' : 'nullable',
            'type'         => 'required|in:Single,EP,Album',
            'genre'        => 'required',
            'release_date' => 'required|date|after_or_equal:' . now()->addDays(14)->toDateString(),
            'cover_path'   => 'required',
        ]);

        // Determine which artist this release is for
        if ($user->role === 'artist') {
            $artist = $user->artist;
        } elseif ($user->role === 'label') {
            $artistId = $request->input('artist_id');
            $artist = Artist::query()->find($artistId);

            if (!$artist) {
                return back()->withErrors(['artist' => 'Selected artist not found.']);
            }

            // Optional: ensure this artist belongs to the label

            if ($artist->label_id !== $user->label->id) {
                return back()->withErrors(['artist' => 'You do not have permission to upload for this artist.']);
            }
        } else {
            abort(403);
        }

        if (!$artist->isProfileComplete()) {
            $redirect = $user->role === 'label'
                ? redirect()->route('artists.edit', $artist)
                : redirect()->route('profile.edit');

            return $redirect->with('error', 'Please complete your profile before uploading a release.');
        }

        // Move cover from temp → final
        $coverPath = $this->moveCover($request->cover_path);

        // 1. Create Project
        $project = Project::create([
            'user_id'      => $user->id,
            'title'        => $request->title,
            'type'         => $request->type,
            'cover_path'   => $coverPath,
            'release_date' => $request->release_date,
            'genre'        => $request->genre,
            'subgenre'     => $request->subgenre,
            'language'     => $request->language,
            'explicit'     => $request->explicit === 'yes',
            'label'        => $request->label,
            'songwriter'   => $request->songwriter,
            'upc'          => $request->upc,
        ]);

        // 2. Validate tracks
        if (!$request->has('tracks') || count($request->tracks) === 0) {
            return back()->withErrors(['tracks' => 'You must add at least one track.']);
        }

        $trackNumber = 1;

        // 3. Create Tracks
        foreach ($request->tracks as $track) {
            if (empty($track['audio_path'])) {
                return back()->withErrors(['tracks' => 'Each track must finish uploading audio before submitting.']);
            }

            Music::create([
                'project_id'       => $project->id,
                'user_id'          => $user->id,
                'title'            => $track['title'],
                'artist'           => $artist->name,
                'genre'            => $request->genre,
                'featured_artists' => $track['featured_artists'] ?? null,
                'audio_path'       => $track['audio_path'],
                'cover_path'       => $coverPath,
                'isrc'             => $track['isrc'] ?? null,
                'credits'          => $track['credits'] ?? null,
                'track_number'     => $trackNumber++,
                'release_date'     => $request->release_date,
                'status'           => $this->trackStatusFor($track['audio_path']),
            ]);

            $this->flagUserIfBlocked($user, $track['audio_path']);
        }
        if ($user && $user->email) {
            // Refresh to ensure project and its tracks are fully saved
            $project->refresh()->load(['user', 'tracks']);

            // Send email notification
            NotificationService::releaseSubmitted($project);
        } else {
            Log::error("User email missing for project ID: " . $project->id);
        }


        return back()->with('status', $request->type . ' uploaded successfully!');
    }
    /**
     * Run the ACRCloud copyright scan on a freshly stitched master file
     * and record the verdict. Non-fatal: any scanner failure is logged
     * and ignored so uploads are never interrupted.
     *
     * @param  string  $path  Absolute path to the assembled master file.
     * @return void
     */
    protected function inspectMasterFile(string $path): void
    {
        try {
            $scanner = app(AudioScannerService::class);

            if (!$scanner->enabled()) {
                return;
            }

            $result = $scanner->scan($path) ?? [];
            $verdict = $scanner->interpret($result);

            CopyrightScan::updateOrCreate(['audio_path' => 'songs/' . basename($path)], [
                'user_id'        => Auth::user()?->id ?? null,
                'status'         => $verdict['status'],
                'matched_title'  => $verdict['matched_title'],
                'matched_artist' => $verdict['matched_artist'],
                'response'       => json_encode($result),
            ]);
        } catch (Exception $e) {
            Log::error('ACRCloud scan failed for ' . $path . ': ' . $e->getMessage());
        }
    }

    /**
     * Resolve the initial status for a new track based on its scan result.
     *
     * @param  string  $audioPath  Stored audio path (e.g. songs/xxx.wav).
     * @return string
     */
    protected function trackStatusFor(string $audioPath): string
    {
        $scan = CopyrightScan::where('audio_path', $audioPath)->first();

        return $scan && $scan->status === 'blocked' ? 'blocked' : 'pending';
    }

    /**
     * Flag the uploader's account when one of their tracks was matched
     * by the copyright scanner.
     *
     * @param  \App\Models\User  $user
     * @param  string            $audioPath
     * @return void
     */
    protected function flagUserIfBlocked($user, string $audioPath): void
    {
        $scan = CopyrightScan::where('audio_path', $audioPath)->first();

        if ($scan && $scan->status === 'blocked' && !$user->flagged) {
            $user->flagged = true;
            $user->save();
        }
    }

    private function moveCover($coverPath)
    {
        if (!str_starts_with($coverPath, 'temp/')) {
            return $coverPath;
        }

        $finalCover = str_replace('temp/', '', $coverPath);
        $from = public_path($coverPath);
        $to   = public_path($finalCover);

        @mkdir(dirname($to), 0777, true);
        @rename($from, $to);

        return $finalCover;
    }
}
