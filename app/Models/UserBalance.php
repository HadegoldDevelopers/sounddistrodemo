<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBalance extends Model
{
    protected $table = 'user_balances';

    protected $fillable = [
        'user_id',
        'music_id',
        'amount',
        'streams',
        'month',
        'year',
        'status',
        'import_hash',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
    ];

    /**
     * The user that owns this balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The music track this balance is associated with (optional).
     */
    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class);
    }

    /**
     * Scope to only approved balances.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to only pending balances.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
  public function getApprovedDateAttribute()
{
    if ($this->year && $this->month) {
        return \Carbon\Carbon::createFromDate($this->year, $this->month);
    }

    return null;
}



}
