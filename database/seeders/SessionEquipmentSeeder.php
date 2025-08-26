<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\CourseSession;
use App\Models\SessionEquipment;
use Illuminate\Database\Seeder;

class SessionEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $sessionEquipment = [
            [
                'course_code' => 'INFO101',
                'start_at' => '2024-09-02 14:00:00',
                'equipment_type' => 'PC Lab',
                'is_mobile' => false,
            ],
            [
                'course_code' => 'INFO101',
                'start_at' => '2024-09-02 09:00:00',
                'equipment_type' => 'Projecteur',
                'is_mobile' => false,
            ],
            [
                'course_code' => 'MATH201',
                'start_at' => '2024-09-03 10:00:00',
                'equipment_type' => 'Projecteur',
                'is_mobile' => false,
            ],
            [
                'course_code' => 'PROG301',
                'start_at' => '2024-09-04 13:00:00',
                'equipment_type' => 'PC Lab',
                'is_mobile' => false,
            ],
            [
                'course_code' => 'PROG301',
                'start_at' => '2024-09-04 13:00:00',
                'equipment_type' => 'Projecteur',
                'is_mobile' => false,
            ],
        ];

        foreach ($sessionEquipment as $item) {
            $session = CourseSession::whereHas('course', function ($query) use ($item) {
                $query->where('code', $item['course_code']);
            })->where('start_at', $item['start_at'])->first();
            
            $equipment = Equipment::whereHas('type', function ($query) use ($item) {
                $query->where('label', $item['equipment_type']);
            })->where('is_mobile', $item['is_mobile'])->first();
            
            if ($session && $equipment) {
                SessionEquipment::create([
                    'session_id' => $session->id,
                    'equipment_id' => $equipment->id,
                ]);
            }
        }
    }
}
