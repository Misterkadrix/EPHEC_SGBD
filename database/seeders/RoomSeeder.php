<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Site;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'site_name' => 'Campus Principal',
                'name' => 'Salle A101',
                'capacity' => 30,
                'description' => 'Salle de cours standard',
            ],
            [
                'site_name' => 'Campus Principal',
                'name' => 'Salle A102',
                'capacity' => 25,
                'description' => 'Salle de cours standard',
            ],
            [
                'site_name' => 'Campus Principal',
                'name' => 'Labo Informatique B201',
                'capacity' => 20,
                'description' => 'Laboratoire informatique avec postes de travail',
            ],
            [
                'site_name' => 'Campus Technologique',
                'name' => 'Amphithéâtre Tech1',
                'capacity' => 100,
                'description' => 'Grand amphithéâtre pour conférences',
            ],
            [
                'site_name' => 'Campus Technologique',
                'name' => 'Salle de Projet P301',
                'capacity' => 15,
                'description' => 'Salle pour travaux de groupe',
            ],
            [
                'site_name' => 'Site Woluwe',
                'name' => 'Salle W101',
                'capacity' => 35,
                'description' => 'Salle de cours EPHEC',
            ],
        ];

        foreach ($rooms as $room) {
            $site = Site::where('name', $room['site_name'])->first();
            if ($site) {
                Room::create([
                    'site_id' => $site->id,
                    'name' => $room['name'],
                    'capacity' => $room['capacity'],
                    'description' => $room['description'],
                ]);
            }
        }
    }
}
