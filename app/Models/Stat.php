<?php

// app/Models/Stat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Stat extends Model
{
    protected $fillable = ['year', 'month', 'music_id', 'store', 'country', 'quality', 'streams', 'user_id', 'artist_id', 'earnings','import_hash'];

    public function music()
    {
        return $this->belongsTo(Music::class);
    }
    
    public function artist() {
    return $this->belongsTo(Artist::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}

}
