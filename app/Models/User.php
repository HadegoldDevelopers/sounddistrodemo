<?php

namespace App\Models;

use App\Models\Music;
use App\Models\Artist;
use App\Models\Project;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'is_sub',
        'sub_expires_at',
        'role',
        'wallet_balance',
        'email_notifications',
        'flagged',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the music uploads for the user.
     */
    public function music(): HasMany
    {
        return $this->hasMany(Music::class);
    }
    public function projects(): HasMany
{
    return $this->hasMany(Project::class);
}
    public function artist()
    {
        return $this->hasOne(Artist::class);
    }

    public function label()
    {
        return $this->hasOne(Label::class);
    }
    public function stats()
    {
        return $this->hasMany(Stat::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getPlanNameAttribute()
    {
        $transaction = $this->transactions()->latest()->first();

        return $transaction?->subscription_plan?->name;
    }
    public function getAuthPassword()
    {
        if (!$this->is_active) {
            return null;
        }
        return $this->password;
    }
}
