<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'site_name',
                'value' => 'WhiteLabel',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-03-13 17:39:05',
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'site_tagline',
                'value' => 'Empowering artists and labels with smart, reliable digital solutions.',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 02:51:07',
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'site_url',
                'value' => '',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2025-08-03 20:28:03',
            ),
            3 => 
            array (
                'id' => 4,
                'key' => 'site_title',
                'value' => 'Digital Music Distribution',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 03:38:05',
            ),
            4 => 
            array (
                'id' => 5,
                'key' => 'site_logo',
                'value' => '/public/uploads/site/1767482912.png',
                'created_at' => '2025-08-03 20:13:44',
                'updated_at' => '2026-01-04 05:28:32',
            ),
            5 => 
            array (
                'id' => 6,
                'key' => 'favicon',
                'value' => 'public/uploads/favicon/1767901370.JPG',
                'created_at' => '2025-08-03 20:13:44',
                'updated_at' => '2026-01-09 01:42:50',
            ),
            6 => 
            array (
                'id' => 7,
                'key' => 'allow_user_registration',
                'value' => '1',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 08:21:43',
            ),
            7 => 
            array (
                'id' => 8,
                'key' => 'enable_email_verification',
                'value' => '0',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-03-08 03:40:47',
            ),
            8 => 
            array (
                'id' => 9,
                'key' => 'meta_keywords_default',
                'value' => 'music distribution, digital music distribution, independent music distribution, distribute music online, music for independent artists, spotify distribution, apple music distribution, music royalties, music monetization, online music distributor',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 02:46:36',
            ),
            9 => 
            array (
                'id' => 10,
                'key' => 'meta_description_default',
                'value' => 'Release Your Music with demo',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 01:43:35',
            ),
            10 => 
            array (
                'id' => 11,
                'key' => 'force_https',
                'value' => '1',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 05:17:13',
            ),
            11 => 
            array (
                'id' => 12,
                'key' => 'robots_txt',
                'value' => 'User-agent: *
Disallow: /admin/
Allow: /',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2025-08-03 09:53:22',
            ),
            12 => 
            array (
                'id' => 13,
                'key' => 'contact_email',
                'value' => 'info@example.com',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-03 16:32:45',
            ),
            13 => 
            array (
                'id' => 14,
                'key' => 'contact_phone',
                'value' => NULL,
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 06:11:40',
            ),
            14 => 
            array (
                'id' => 15,
                'key' => 'contact_address',
                'value' => NULL,
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 06:11:40',
            ),
            15 => 
            array (
                'id' => 17,
                'key' => 'google_site_verification',
                'value' => 'YOUR_CODE_HERE',
                'created_at' => '2026-01-09 02:26:09',
                'updated_at' => '2026-01-09 03:44:06',
            ),
            16 => 
            array (
                'id' => 52,
                'key' => 'instagram',
                'value' => '',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 06:28:55',
            ),
            17 => 
            array (
                'id' => 63,
                'key' => 'linkedin',
                'value' => '',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 06:36:18',
            ),
            18 => 
            array (
                'id' => 65,
                'key' => 'twitter',
                'value' => NULL,
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 06:28:55',
            ),
            19 => 
            array (
                'id' => 66,
                'key' => 'Facebook',
                'value' => NULL,
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-09 06:28:55',
            ),
            20 => 
            array (
                'id' => 67,
                'key' => 'allow_label_registration',
                'value' => '0',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-01-30 01:51:57',
            ),
            21 => 
            array (
                'id' => 68,
                'key' => 'require_subscription',
                'value' => '1',
                'created_at' => '2025-08-03 09:53:22',
                'updated_at' => '2026-03-08 03:40:29',
            ),
            22 => 
            array (
                'id' => 69,
                'key' => 'terms_pdf',
                'value' => '',
                'created_at' => '2026-01-09 09:00:43',
                'updated_at' => '2026-01-09 09:29:19',
            ),
        ));
        
        
    }
}