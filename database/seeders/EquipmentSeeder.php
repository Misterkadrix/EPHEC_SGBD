<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Room;
use App\Models\Site;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipment = [
            [
                'site_name' => 'Campus Principal',
                'type_label' => 'PC Lab',
                'is_mobile' => false,
                'room_name' => 'Labo Informatique B201',
            ],
            [
                'site_name' => 'Campus Principal',
                'type_label' => 'Projecteur',
                'is_mobile' => false,
                'room_name' => 'Salle A101',
            ],
            [
                'site_name' => 'Campus Principal',
                'type_label' => 'Projecteur',
                'is_mobile' => false,
                'room_name' => 'Salle A102',
            ],
            [
                'site_name' => 'Campus Technologique',
                'type_label' => 'Projecteur',
                'is_mobile' => false,
                'room_name' => 'Amphithéâtre Tech1',
            ],
            [
                'site_name' => 'Campus Technologique',
                'type_label' => 'Système Audio',
                'is_mobile' => false,
                'room_name' => 'Amphithéâtre Tech1',
            ],
            [
                'site_name' => 'Campus Principal',
                'type_label' => 'Ordinateur Portable',
                'is_mobile' => true,
                'room_name' => null,
            ],
            [
                'site_name' => 'Campus Principal',
                'type_label' => 'Tablette',
                'is_mobile' => true,
                'room_name' => null,
            ],
            [
                'site_name' => 'Site Woluwe',
                'type_label' => 'Projecteur',
                'is_mobile' => false,
                'room_name' => 'Salle W101',
            ],
        ];

        foreach ($equipment as $item) {
            $site = Site::where('name', $item['site_name'])->first();
            $type = EquipmentType::where('label', $item['type_label'])->first();
            $room = null;
            
            if ($item['room_name']) {
                $room = Room::where('name', $item['room_name'])->first();
            }

            if ($site && $type) {
                Equipment::create([
                    'site_id' => $site->id,
                    'type_id' => $type->id,
                    'is_mobile' => $item['is_mobile'],
                    'fixed_room_id' => $room ? $room->id : null,
                ]);
            }
        }
    }
}
