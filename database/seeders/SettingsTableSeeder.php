<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'SunuNews', 'type' => 'string'],
            ['key' => 'site_logo', 'value' => 'images/logo-sununews.png', 'type' => 'string'],
            ['key' => 'primary_color', 'value' => '#1a73e8', 'type' => 'string'],
            ['key' => 'default_language', 'value' => 'fr', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            // updateOrCreate keyed on the unique key, so a second `db:seed`
            // does not abort on settings_key_unique.
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
