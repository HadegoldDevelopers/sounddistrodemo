<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class SitemapGenerator
{
  public static function generate(): void
  {
    $baseUrl = rtrim(\App\Models\Setting::getValue('site_url'), '/');
    $date    = now()->toAtomString();

    $urls = [
      ['loc' => $baseUrl . '/',           'priority' => '1.0', 'changefreq' => 'daily'],
      ['loc' => $baseUrl . '/terms',       'priority' => '0.5', 'changefreq' => 'monthly'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $url) {
      $xml .= "    <url>\n";
      $xml .= "        <loc>{$url['loc']}</loc>\n";
      $xml .= "        <lastmod>{$date}</lastmod>\n";
      $xml .= "        <changefreq>{$url['changefreq']}</changefreq>\n";
      $xml .= "        <priority>{$url['priority']}</priority>\n";
      $xml .= "    </url>\n";
    }

    $xml .= '</urlset>';

    File::put(base_path('sitemap.xml'), $xml);
  }
}