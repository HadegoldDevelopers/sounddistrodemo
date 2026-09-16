<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Label;
use Carbon\Carbon;
use App\Models\Stat;
use App\Models\Music;
use App\Models\Project;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function userDashboard()
{
    $user = Auth::user();

    // Basic counts
    $submittedSongs = $user->music()->count();
    $approvedSongs  = $user->music()->where('status', 'approved')->count();
    $totalArtists   = $user->artist()->count();

    // Date ranges
    $last30Days        = now()->subDays(30);
    $startOfThisMonth  = now()->startOfMonth();
    $startOfLastMonth  = now()->subMonth()->startOfMonth();
    $endOfLastMonth    = now()->subMonth()->endOfMonth();

    // Stats (streams + earnings)
    $totalStreams = UserBalance::where('user_id', $user->id)
        ->where('created_at', '>=', $last30Days)
        ->sum('streams');
        
        // Earnings last 30 days
    $totalEarnings = UserBalance::where('user_id', $user->id)
        ->approved()
        ->where('created_at', '>=', $last30Days)
        ->sum('amount');
        
    // Upload comparison
    $thisMonthUploads = $user->music()
        ->where('created_at', '>=', $startOfThisMonth)
        ->count();

    $lastMonthUploads = $user->music()
        ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
        ->count();

    $percentChange = 0;
    if ($lastMonthUploads > 0) {
        $percentChange = (($thisMonthUploads - $lastMonthUploads) / $lastMonthUploads) * 100;
    } elseif ($thisMonthUploads > 0) {
        $percentChange = 100;
    }

    // Recent uploads
    $musics = $user->music()->latest()->take(5)->get();

    // Chart data
    $streamChartData = Stat::selectRaw("
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(streams) as total_streams
    ")
    ->where('user_id', $user->id)
    ->where('created_at', '>=', now()->subMonths(6))
    ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
    ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')")
    ->get();

    // Projects
    $projects = Project::with('tracks')
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    return view('dashboard', [
        'user'            => $user,
        'submittedSongs'  => $submittedSongs,
        'approvedSongs'   => $approvedSongs,
        'musics'          => $musics,
        'percentChange'   => round($percentChange, 2),
        'totalStreams'    => $totalStreams,
        'totalEarnings'   => round($totalEarnings, 2),
        'streamChartData' => $streamChartData,
        'totalArtists'    => $totalArtists,
        'projects'        => $projects,
    ]);
}


    public function release()
{ 
    $user = Auth::user();

    // Load all projects (releases) created by this user
    $releases = Project::with(['tracks'])  
        ->where('user_id', $user->id)
        ->latest()
        ->paginate(10);

    return view('music.releases', compact('releases', 'user'));
}
  public function showRelease(Project $project)
{
    // Ownership check: a user may only view their own releases (or releases
    // created by artists in their label). Prevents IDOR across accounts.
    $user = Auth::user();

    $owner = $project->user;

    if ($owner === null || $owner->id !== $user->id) {
        $isLabelArtist = $user->role === 'label'
            && $owner->role === 'artist'
            && $owner->label_id === $user->label?->id;

        if (!$isLabelArtist) {
            abort(403, 'You do not have access to this release.');
        }
    }

    // Build artist + featured artists
    $mainArtists = $project->tracks->pluck('artist')->filter()->unique();
    $featured = $project->tracks
        ->pluck('featured_artists')
        ->filter()
        ->flatMap(fn($v) => array_map('trim', explode(',', $v)))
        ->unique();

    if ($mainArtists->count() === 1) {
        $artistName = $mainArtists->first();
        if ($featured->count()) {
            $artistName .= ' feat. ' . $featured->join(', ');
        }
    } else {
        $artistName = 'Various Artists';
    }

    return response()->json([
        'title' => $project->title,
        'type' => $project->type,
        'upc' => $project->upc,
        'cover' => assetPath($project->cover_path),
        'artist' => $artistName,
        'release_date' => optional($project->release_date)->format('M d, Y'),
        'genre' => $project->genre,
        'subgenre' => $project->subgenre,
        'label' => $project->label,
        'explicit' => $project->explicit,

        'tracks' => $project->tracks()
            ->select(
                'id',
                'title',
                'track_number',
                'artist',
                'featured_artists',
                'credits',
                'isrc'
            )
            ->orderBy('track_number')
            ->get()
            ->map(function ($track) {
                return [
                    'title' => $track->title,
                    'track_number' => $track->track_number,
                    'artist' => $track->artist,
                    'featured_artists' => $track->featured_artists,
                    'credits' => $track->credits,
                    'isrc' => $track->isrc,
                ];
            }),
    ]);
}


    public function settings()
    {
        $user = Auth::user();
        return view('user.settings', compact('user'));
    }
    
    public function updateSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Update only the fields you want
        $user->email_notifications = $request->email_notifications;
        $user->save();

        return Redirect::route('user.settings')->with('status', 'Settings updated successfully.');
    }
}