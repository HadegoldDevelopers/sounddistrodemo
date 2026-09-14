<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $fillable = [
        'user_id',
        'label_name',
        'description',
        'website',
        'logo',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artist()
    {
        return $this->hasMany(Artist::class);
    }
}
