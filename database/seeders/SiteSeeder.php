<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\University;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            [
                'university_code' => 'KAD',
                'name' => 'Campus Principal',
                'timezone' => 'Europe/Brussels',
                'day_start' => '08:00',
                'day_end' => '18:00',
                'active_days' => ['MO', 'TU', 'WE', 'TH', 'FR'],
            ],
            [
                'university_code' => 'KAD',
                'name' => 'Campus Technologique',
                'timezone' => 'Europe/Brussels',
                'day_start' => '07:30',
                'day_end' => '19:30',
                'active_days' => ['MO', 'TU', 'WE', 'TH', 'FR', 'SA'],
            ],
            [
                'university_code' => 'EPHEC',
                'name' => 'Site Woluwe',
                'timezone' => 'Europe/Brussels',
                'day_start' => '08:00',
                'day_end' => '17:00',
                'active_days' => ['MO', 'TU', 'WE', 'TH', 'FR'],
            ],
        ];

        foreach ($sites as $site) {
            $university = University::where('code', $site['university_code'])->first();
            if ($university) {
                Site::create([
                    'university_id' => $university->id,
                    'name' => $site['name'],
                    'timezone' => $site['timezone'],
                    'day_start' => $site['day_start'],
                    'day_end' => $site['day_end'],
                    'active_days' => $site['active_days'],
                ]);
            }
        }
    }
}
