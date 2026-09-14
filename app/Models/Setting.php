<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    protected $table = 'settings';
    protected $casts = [
        'available_languages' => 'array',
    ];

    // Fetch single setting value by key (with caching)
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("global_{$key}", 300, function () use ($key, $default) {
            $global = self::where('key', $key)->first();
            return $global ? $global->value : $default;
        });
    }

    // Save or update a setting value (clear cache)
    public static function setValue(string $key, $value)
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("global_{$key}");
    }
}
