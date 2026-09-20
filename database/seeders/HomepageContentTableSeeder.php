<?php

namespace Database\Seeders;

use App\Models\HomepageContent;
use Illuminate\Database\Seeder;

class HomepageContentTableSeeder extends Seeder
{
    /**
     * Seed a default homepage content row so a fresh installation has
     * usable landing-page content (images included) out of the box.
     */
    public function run(): void
    {
        if (HomepageContent::exists()) {
            return;
        }

        HomepageContent::create([
            'hero_title'      => 'Create your Art. Make your Money. Repeat',
            'hero_text'       => 'Distribute your songs to 150+ platforms and get paid every month.',
            'hero_image'      => 'images/home/hero.jpg',

            'features_title'  => 'Powerful Features',
            'features_text'   => 'Everything you need to distribute your music worldwide.',
            'features'        => [
                ['icon' => 'fa-music', 'title' => 'Distribution', 'text' => 'Distribution to all platforms'],
                ['icon' => 'fa-headset', 'title' => 'Support', 'text' => 'Real support from musicians like you'],
                ['icon' => 'fa-users', 'title' => 'Collaboration', 'text' => 'Automated collaborator splits'],
                ['icon' => 'fa-balance-scale', 'title' => 'Transparent Pricing', 'text' => 'Simple, transparent pricing'],
                ['icon' => 'fa-certificate', 'title' => 'Cover Songs', 'text' => 'Cover-song licensing included'],
                ['icon' => 'fa-clock', 'title' => 'No Upfront Costs', 'text' => 'Pay nothing until you are ready'],
            ],

            'labels_title'    => 'Built for Labels. Trusted by Artists.',
            'labels_text'     => 'Distroflow connects your music with the right audience. Access playlist pitching, cross-platform marketing, and royalty tools designed for independent labels.',
            'labels_image'    => 'images/home/canoe.jpg',

            'pricing_title'   => 'Simple Pricing. No hidden fees.',
            'pricing_text'    => 'Choose the plan that fits you. No complicated tiers.',

            'artists'         => [
            ['name' => 'RAJESH SYANGTAN', 'genre' => 'Soul', 'image' => 'images/artists/rajesh.jpg', 'overlay_color' => '#FF4F81'],
            ['name' => 'ZAAKY BUDDY', 'genre' => 'Hip-Hop · United States', 'image' => 'images/artists/zaaky.jpg', 'overlay_color' => '#A020F0'],
            ['name' => 'ISMAIL SULEIMAN', 'genre' => 'Amapiano · South Africa', 'image' => 'images/artists/ismail.jpg', 'overlay_color' => '#FF8C00'],
            ['name' => 'ANTONI SHKRABA', 'genre' => 'Afrobeats · Nigeria', 'image' => 'images/artists/antoni.jpg', 'overlay_color' => '#4FD1C5'],
        ],

            'cta_title_1'     => 'Ready to Launch',
            'cta_title_2'     => 'Your Music?',
            'cta_text'        => 'Get started today with a free account.',
        ]);
    }
}
