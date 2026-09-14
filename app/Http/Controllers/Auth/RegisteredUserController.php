<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Setting;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
   

    public function create(): View|RedirectResponse
{
    if (!Setting::getValue('allow_user_registration', 0)) {
        abort(403, 'User registration is disabled.');
    }

    if (Auth::check()) {
        return redirect()->route('user.dashboard');
    }

    return view('auth.register');
}


    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Setting::getValue('allow_user_registration', 0)) {
            abort(403, 'User registration is disabled.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|in:artist,label',
            'terms' => 'accepted',
        ]);
        
        if (
            $request->role === 'label' &&
            !Setting::getValue('allow_label_registration', 0)
        ) {
            abort(403, 'Label registration is disabled.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'wallet_balance' => 0,
            'is_sub' => 0,
            'sub_expires_at' => null
        ]);

        if($user->role === 'artist') {
            // Create associated artist record
            $user->artist()->create([
                'name' => $user->name,
                'email' => $user->email,
            ]);
            
        } elseif ($user->role === 'label') {
            // Create associated label record
            $user->label()->create([
                'label_name' => $user->name,
                'description' => '',
                'website' => '',
                'logo' => '',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }
}
