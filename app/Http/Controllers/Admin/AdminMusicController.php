<?php

namespace App\Http\Controllers\Admin;

use App\Services\NotificationService;
use App\Models\Music;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;


class AdminMusicController extends Controller
{
    public function index()
    {
        $data = Project::with(['tracks.copyrightScan'])
    ->latest()
    ->paginate(10);
    
        return view('admin.releases.index', compact('data'));
    }
    
    public function show(Project $release)
    {
        $release->load('tracks');
        
        return view('admin.releases.show', compact('release'));
    }
    
    public function downloadAudio(Music $release)
{
    $disk = Storage::disk('songs');

    // Remove "songs/" prefix if your DB stores it
    $path = str_replace('songs/', '', $release->audio_path);

    abort_unless($disk->exists($path), 404);

    $filename = str($release->title)->slug('-') . '.mp3';

    return $disk->download($path, $filename, [
        'Content-Type' => 'audio/mpeg',
    ]);
}



    public function downloadCover(Project $release): StreamedResponse
{
    $disk = Storage::disk('covers');

    // Remove folder prefix if stored as "covers/filename.jpg"
    $path = str_replace('covers/', '', $release->cover_path);

    abort_unless($disk->exists($path), 404);

    $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
    $filename = str($release->title)->slug('-') . '.' . $ext;

    return $disk->download($path, $filename);
}


public function downloadMetadata(Project $release)
{
    $release->load(['tracks', 'user.artist']);

    $artist = $release->user?->artist;

    // Project-level metadata
    $projectPayload = [
        'title'              => $release->title,
        'type'               => $release->type,
        'genre'              => $release->genre,
        'subgenre'           => $release->subgenre,
        'language'           => $release->language,
        'explicit'           => $release->explicit,
        'release_date'       => $release->release_date->toDateString(),
        'status'             => $release->status,
        'cover_path'         => $release->cover_path,
        'project_label'      => $release->label,
        'project_songwriter' => $release->songwriter,
        'project_upc'        => $release->upc,
        'created_at'         => $release->created_at?->toIso8601String(),
        'updated_at'         => $release->updated_at?->toIso8601String(),
    ];

    // Artist store & social links
    $artistPayload = [
        'name'            => $artist?->name ?? $release->user?->name,
        'spotify'         => $artist?->spotify_id,
        'apple_music'     => $artist?->apple_music_id,
        'audiomack'       => $artist?->audiomack_id,
        'facebook'        => $artist?->facebook,
        'instagram'       => $artist?->instagram,
        'twitter'         => $artist?->twitter,
        'tiktok'          => $artist?->tiktok,
    ];

    // Tracks metadata
    $tracksPayload = $release->tracks->map(fn($track) => [
        'title'            => $track->title,
        'artist'           => $track->artist,
        'featured_artists' => $track->featured_artists,
        'producer'         => $track->credits,
        'isrc'             => $track->isrc,
        'audio_path'       => $track->audio_path,
    ]);

    $payload = [
        'project' => $projectPayload,
        'artist'  => $artistPayload,
        'tracks'  => $tracksPayload,
    ];

    // JSON
    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    // CSV — project + artist info as header block, then tracks
    $csvContent  = "RELEASE INFORMATION\n";
    $csvContent .= implode(',', array_keys($projectPayload)) . "\n";
    $csvContent .= implode(',', array_map(fn($v) => '"' . ($v ?? '') . '"', $projectPayload)) . "\n";

    $csvContent .= "\nARTIST & STORE LINKS\n";
    $csvContent .= implode(',', array_keys($artistPayload)) . "\n";
    $csvContent .= implode(',', array_map(fn($v) => '"' . ($v ?? '') . '"', $artistPayload)) . "\n";

    $csvContent .= "\nTRACKS\n";
    $csvContent .= implode(',', ['track_title', 'artist', 'featured_artists', 'producer', 'isrc', 'audio_path']) . "\n";
    foreach ($tracksPayload as $row) {
        $csvContent .= implode(',', array_map(fn($v) => '"' . ($v ?? '') . '"', $row)) . "\n";
    }

    // PDF
    $pdf = PDF::loadView('admin.pdf.metadata', [
        'project' => $projectPayload,
        'artist'  => $artistPayload,
        'tracks'  => $tracksPayload,
    ])->output();

    $zipFileName = Str::slug($release->title) . '-metadata.zip';

    return response()->streamDownload(function () use ($json, $csvContent, $pdf) {
        $zip = new ZipArchive;
        $tmp = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tmp, ZipArchive::CREATE) === true) {
            $zip->addFromString('metadata.json', $json);
            $zip->addFromString('metadata.csv',  $csvContent);
            $zip->addFromString('metadata.pdf',  $pdf);
            $zip->close();
        }

        echo file_get_contents($tmp);
        unlink($tmp);
    }, $zipFileName, [
        'Content-Type' => 'application/zip',
    ]);
}


    public function metadataEdit(Project $project)
{
    $project->load('tracks');

    return view('admin.releases.metadata', compact('project'));
}

    public function updateMetadata(Request $request, Project $project)
{
    $project->update([
        'title' => $request->title,
        'artist_display' => $request->artist_display,
        'genre' => $request->genre,
        'release_date' => $request->release_date,
        'upc' => $request->upc,
    ]);

    if ($request->has('tracks')) {
        foreach ($request->tracks as $trackData) {
            $track = Music::find($trackData['id']);

            if ($track) {
                $track->update([
                    'artist' => $trackData['artist'] ?? null,
                    'featured_artists' => $trackData['featured_artists'] ?? null,
                    'isrc' => $trackData['isrc'] ?? null,
                    'credits' => $trackData['credits'] ?? null,
                ]);
            }
        }
    }

    return redirect()
        ->route('admin.releases.all')
        ->with('status', 'Metadata updated successfully.');
}
    
    public function approveProject($id)
{
    $project = Project::findOrFail($id);

    $project->tracks->each(function($track){
        $track->status = 'approved';
        $track->save();
    });
    
    // Send email notification
    
    NotificationService::releaseApproved($project);
    
    return redirect()->back()->with('success', 'Project approved.');
}

public function rejectProject($id)
{
    $project = Project::findOrFail($id);

    $project->tracks->each(function($track){
        $track->status = 'rejected';
        $track->save();
    });
NotificationService::releaseRejected($project);
    return redirect()->back()->with('error', 'Project rejected.');
}
    
    public function status($status)
{
    $allowed = ['pending', 'approved', 'rejected'];

    if (!in_array($status, $allowed)) {
        abort(404);
    }

    $data = Project::with('tracks')
        ->where(function ($query) use ($status) {
            if ($status === 'approved') {
                // all tracks approved
                $query->whereDoesntHave('tracks', fn($q) => $q->where('status', '!=', 'approved'));
            } elseif ($status === 'rejected') {
                // any track rejected
                $query->whereHas('tracks', fn($q) => $q->where('status', 'rejected'));
            } elseif ($status === 'pending') {
                // any track pending and no rejected tracks
                $query->whereHas('tracks', fn($q) => $q->where('status', 'pending'))
                      ->whereDoesntHave('tracks', fn($q) => $q->where('status', 'rejected'));
            }
        })
        ->latest()
        ->paginate(10);

    return view('admin.releases.status', compact('data', 'status'));
}

public function destroy($id)
{
    $project = Project::with('tracks')->findOrFail($id);

    // Delete cover
    if (!empty($project->cover_path)) {

        $coverPath = str_replace('covers/', '', $project->cover_path);
        Storage::disk('covers')->delete($coverPath);
    }

    // Delete metadata
    if (!empty($project->metadata_path)) {
        Storage::disk('public')->delete($project->metadata_path);
    }

    // Delete audio files + track rows
    foreach ($project->tracks as $track) {

        $path = str_replace('songs/', '', $track->audio_path);
        Storage::disk('songs')->delete($path);

        $track->delete();
    }

    // Delete the project
    $project->delete();

    return back()->with('success', 'Release deleted successfully.');
}

}
