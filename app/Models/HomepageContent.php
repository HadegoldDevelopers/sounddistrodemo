<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class HomepageContent extends Model
{
    /**
     * The database table name.
     * Declaring this explicitly avoids routing bugs across database configurations.
     */
    protected $table = 'homepage_contents';

    /**
     * The attributes that are mass assignable.
     * Buyers can safely update landing configurations via your Admin panel workspace.
     */
    protected $fillable = [
        // Hero
        'hero_title',
        'hero_text',
        'hero_image',

        // Features (JSON array of icon + text)
        'features',
        'features_title',
        'features_text',

        // Labels section
        'labels_title',
        'labels_text',
        'labels_image',

        // Pricing
        'pricing_title',
        'pricing_text',

        // Artists (JSON array: name, genre, image, overlay color)
        'artists',

        // CTA
        'cta_title_1',
        'cta_title_2',
        'cta_text',
    ];

    /**
     * FIXED: Modern method-based casting configuration layout.
     * This handles longtext translation structures cleanly on production web environments.
     */
    protected function casts(): array
    {
        return [
            'features'        => 'array',
            'artists'         => 'array',
            'created_at'      => 'datetime',
            'updated_at'      => 'datetime',
        ];
    }

    /**
     * Overriding magic getter to intercept null columns and inject default values instantly.
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        // If the database field is explicitly null, pull from our fallback map
        if (is_null($value)) {
            return match ($key) {
                // Hero
                'hero_title'      => __('Create. Monetize. Repeat.'),
                'hero_text'       => __('SonicDist is built for creators like you. Distribute your songs to 150+ platforms and get paid every month.'),
                'hero_image'      => 'images/home/hero.jpg',

                // Features
                'features_title'  => __('Powerful Features'),
                'features_text'   => __('Everything you need to distribute your music worldwide.'),
                'features'        => $this->getDefaultFeatures(),

                // Labels section
                'labels_title'    => __('Built for Labels. Trusted by Artists.'),
                'labels_text'     => __('Sound Distro connects your music with the right audience. Access playlist pitching, cross‑platform marketing, and royalty tools designed for independent labels.'),
                'labels_image'    => 'images/home/canoe.jpg',

                // Pricing
                'pricing_text'    => __('Simple Pricing. No subscriptions. No surprises.'),

                // Artists
                'artists'         => $this->getDefaultArtists(),

                // CTA
                'cta_title_1'     => __('Ready to Launch'),
                'cta_title_2'     => __('Your Music?'),
                'cta_text'        => __('Get started today with a free Sound Distro account.'),
                
                default           => $value
            };
        }

        return $value;
    }

    /**
     * Fallback array structure for Features block.
     */
    private function getDefaultFeatures(): array
    {
        return [
            ['icon' => 'fa-music', 'text' => __('Distribution to all platforms')],
            ['icon' => 'fa-headset', 'text' => __('Real support from musicians like you')],
            ['icon' => 'fa-users', 'text' => __('Automated collaborator splits')],
            ['icon' => 'fa-balance-scale', 'text' => __('Simple, transparent pricing')],
            ['icon' => 'fa-certificate', 'text' => __('Cover‑song licensing included')],
            ['icon' => 'fa-clock', 'text' => __('Pay nothing until you are ready')],
        ];
    }

    /**
     * Fallback array structure for Artists slider block.
     */
    private function getDefaultArtists(): array
    {
        return [
            ['name' => 'Skales', 'genre' => 'Afrobeats · Nigeria', 'image' => 'images/artist/skales.jpg', 'overlay_color' => '#FF4F81'],
            ['name' => 'Ice Prince', 'genre' => 'Hip-Hop · Nigeria', 'image' => 'images/artist/ice_prince.png', 'overlay_color' => '#A020F0'],
            ['name' => 'Yaba Buluku Boyz', 'genre' => 'Amapiano · Mozambique', 'image' => 'images/artist/yaba_buluku_boy.jpg', 'overlay_color' => '#FF8C00'],
            ['name' => 'DJ Hacord', 'genre' => 'Afrobeats · DJ', 'image' => 'images/artist/DJ_Hacord.png', 'overlay_color' => '#4FD1C5'],
        ];
    }
}
