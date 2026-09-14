<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artist extends Model
{
    public function label()
    {
        return $this->belongsTo(Label::class);
    }
    public function music()
    {
        return $this->hasMany(Music::class, 'user_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['name', 'email', 'bio', 'genre', 'profile_image', 'country', 'user_id', 'label_id',
    
    //Streaming Platforms
        'audiomack_id',
        'spotify_id',
        'apple_music_id',

        //Social Links
        'facebook',
        'instagram',
        'twitter',
        'tiktok',
        

    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function isProfileComplete()
{
    return $this->name 
        && $this->audiomack_id 
        && $this->spotify_id 
        && $this->apple_music_id 
        && $this->instagram 
        && $this->tiktok;
}
}