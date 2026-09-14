<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\UserBalance;
use App\Models\Artist;
use App\Models\Label;
use App\Models\Music;
use App\Models\Project;
use App\Models\Stat;
use App\Models\Setting;
use App\Models\Withdrawal;
use App\Models\Transaction;


class AdminController extends Controller
{
    public function showLoginForm()
    {
        if (app(LicenseService::class)->isAdminBlocked()) {
            return redirect()->route('admin.license');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        if (app(LicenseService::class)->isAdminBlocked()) {
            return redirect()->route('admin.license');
        }

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    public function showLicenseForm()
    {
        return view('admin.license');
    }

    public function updateLicense(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string|max:100',
        ]);

        $license = app(LicenseService::class);

        if (!$license->restore($request->purchase_code)) {
            return back()->with('error', 'Invalid purchase code. Please check and try again.');
        }

        return back()->with('success', 'License verified. You can now log in.');
    }
    
    public function edit()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required','email','max:255',
                Rule::unique('admins')->ignore($admin->id),
            ],
            'password' => 'nullable|string|min:6|confirmed', // password confirmation field required
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully.');
    }
    
    
    public function logout(Request $request)
{
    Auth::guard('admin')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
}


public function dashboard(AnalyticsService $analytics)
{
    return view('admin.dashboard', [

        // KPI Cards
        'totalUsers'        => User::count(),
        'newSignups'        => User::where('created_at', '>=', now()->subDays(30))->count(),
        'last7DaysSignups' => User::where('created_at', '>=', now()->subDays(7))->count(),

        // Total Revenue (converted to USD)
        'totalRevenue' => Stat::get()->sum(function ($s) {
            return convertCurrency($s->earnings, $s->currency ?? 'USD', 'USD');
        }),

        // Active Subscribers
        'activeSubscribers' => User::where('is_sub', true)->count(),

        // Pending Earnings
        'pendingEarnings' => UserBalance::where('status', 'pending')->get()
            ->sum(fn ($b) => convertCurrency($b->amount, $b->currency ?? 'USD', 'USD')),

        // Total Paid Out
        'totalPaidOut' => Withdrawal::where('status', 'paid')->get()
            ->sum(fn ($w) => convertCurrency($w->amount, $w->currency ?? 'USD', 'USD')),

        // Pending Withdrawals
        'pendingWithdrawals' => Withdrawal::where('status', 'pending')->get()
            ->sum(fn ($w) => convertCurrency($w->amount, $w->currency ?? 'USD', 'USD')),

        // Platform Revenue
        'platformRevenue' => Transaction::get()
            ->sum(fn ($t) => convertCurrency($t->amount, $t->currency ?? 'USD', 'USD')),

        // Additional dashboard data
        'totalReleases'     => Project::count(),
        'pendingApprovals'  => Music::where('status', 'pending')->count(),
        'releasesThisMonth' => Project::where('created_at', '>=', now()->startOfMonth())->count(),
        
        // Charts & Lists
        'albumUploads'      => $analytics->albumUploadsLast12Months(),

        'topArtists' => Stat::selectRaw('user_id, SUM(earnings) as total_earnings')
            ->with('user:id,name')
            ->groupBy('user_id')
            ->orderByDesc('total_earnings')
            ->take(5)
            ->get(),

        'mostStreamed' => Stat::selectRaw('music_id, SUM(streams) as total_streams')
            ->with('music')
            ->groupBy('music_id')
            ->orderByDesc('total_streams')
            ->take(5)
            ->get(),

        'recentUploads' => Music::latest()->take(5)->get(),
    ]);
}



    public function userIndex()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        $allow_label_registration = Setting::getValue('allow_label_registration', '1');
    
        

        return view('admin.users.index', compact('users','allow_label_registration'));
    }


    public function userAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:artist,label',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        // If the user is an artist, create artist record
        if ($user->role === 'artist') {
            Artist::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
        if ($user->role === 'label') {
            // If user is a label
            Label::create([
                'user_id' => $user->id,
                'label_name' => $user->name,
                'description' => '',
                'website' => '',
                'logo' => '',
            ]);
        }

        return redirect()->route('admin.users.all')->with('success', 'User added successfully');
    }



public function userUpdate(Request $request, $id)
{
    $user    = User::findOrFail($id);
    $oldRole = $user->role;
    $newRole = $request->role ?? $user->role;

    // Validate email uniqueness — ignore the current user's own email
    $request->validate([
        'email' => 'required|email|unique:users,email,' . $id,
        'name'  => 'required|string|unique:users,name,' . $id . '|max:255',
    ]);

    try {
        // Update basic user fields
        $user->update([
            'name'           => $request->name,
            'email'          => $request->email,
            'role'           => $newRole,
            'wallet_balance' => $request->wallet_balance ?? $user->wallet_balance,
            'is_active'      => $request->has('status')
                                    ? $request->status === 'active'
                                    : $user->is_active,
        ]);

       // ── Handle role change ────────────────────────────────────────
    if ($oldRole !== $newRole) {

        // Label → Artist
        if ($oldRole === 'label' && $newRole === 'artist') {

            $label = Label::where('user_id', $user->id)->first();

            if ($label) {
                // Detach all artists under this label — they become independent
                Artist::where('label_id', $label->id)
                    ->update(['label_id' => null]);

                // Delete the label record
                $label->delete();
            }

            // Create artist profile for the user
            Artist::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'  => $request->name,
                    'email' => $user->email,
                ]
            );
        }

        // Artist → Label
        if ($oldRole === 'artist' && $newRole === 'label') {

            // Delete artist profile
            Artist::where('user_id', $user->id)->delete();

            // Create label record
            // New label starts with no artists — admin assigns artists separately
            Label::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'label_name' => $request->label_name ?? $user->name,
                    'email'      => $user->email,
                ]
            );
        }
    }

    // ── Always sync name and email to profile ─────────────────────
    // Runs regardless of whether role changed or not
    if ($newRole === 'artist') {
        Artist::updateOrCreate(
    ['user_id' => $user->id],
    [
        'name'  => $request->name,
        'email' => $request->email,
    ]
);
    }

    if ($newRole === 'label') {
        Label::updateOrCreate(
    ['user_id' => $user->id],
    [
        'label_name' => $request->name,
        'email'      => $request->email,
    ]
);
}
        return redirect()->route('admin.users.all')
            ->with('success', 'User updated successfully');

    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->errorInfo[1] === 1062) {
            return back()->withErrors([
                'email' => 'This email address is already taken by another user.'
            ])->withInput();
        }

        return back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}


public function userDestroy($id)
{
    $user = User::findOrFail($id);

    if ($user->role === 'artist') {
        Artist::where('user_id', $id)->delete();
    }

    if ($user->role === 'label') {
        $label = Label::where('user_id', $id)->first();

        if ($label) {
            // Detach artists under this label before deleting
            Artist::where('label_id', $label->id)
                ->update(['label_id' => null]);

            $label->delete();
        }
    }

    $user->delete();

    return redirect()->route('admin.users.all')
        ->with('success', 'User deleted successfully');
}

public function labels()
{
    $labels = Label::orderBy('created_at', 'desc')->paginate(10);
    return view('admin.users.labels', compact('labels'));
}

public function artists()
{
    $artists = Artist::with(['label', 'music'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('admin.users.artist', compact('artists'));
}

public function editArtist($id)
{
    $artist = Artist::with('user', 'label')->findOrFail($id);
    $labels = User::where('role', 'label')->get();
    return view('admin.users.edit-artist', compact('artist', 'labels'));
}

public function updateArtist(Request $request, $id)
{
    $artist = Artist::findOrFail($id);

    $artist->update([
        'name'     => $request->name,
        'genre'    => $request->genre,
        'label_id' => $request->label_id,
    ]);

    // Sync name back to users table
    $artist->user->update(['name' => $request->name]);

    return redirect()->route('admin.labels.artists')
        ->with('success', 'Artist updated successfully');
}

public function deleteArtist($id)
{
    $artist = Artist::findOrFail($id);

    // Delete the user account linked to this artist
    $artist->user()->delete();

    // Delete the artist profile
    $artist->delete();

    return redirect()->route('admin.labels.artists')
        ->with('success', 'Artist deleted successfully');
}

    public function updateEarnings(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->earnings = $request->earnings;
        $user->save();

        return back()->with('success', 'Earnings updated successfully');
    }
}

