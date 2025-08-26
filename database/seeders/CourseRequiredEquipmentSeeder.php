<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseRequiredEquipment;
use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class CourseRequiredEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $requirements = [
            [
                'course_code' => 'INFO101',
                'equipment_type' => 'PC Lab',
            ],
            [
                'course_code' => 'INFO101',
                'equipment_type' => 'Projecteur',
            ],
            [
                'course_code' => 'MATH201',
                'equipment_type' => 'Projecteur',
            ],
            [
                'course_code' => 'PROG301',
                'equipment_type' => 'PC Lab',
            ],
            [
                'course_code' => 'PROG301',
                'equipment_type' => 'Projecteur',
            ],
            [
                'course_code' => 'BUS101',
                'equipment_type' => 'Projecteur',
            ],
            [
                'course_code' => 'PHYS101',
                'equipment_type' => 'Projecteur',
            ],
        ];

        foreach ($requirements as $requirement) {
            $course = Course::where('code', $requirement['course_code'])->first();
            $equipmentType = EquipmentType::where('label', $requirement['equipment_type'])->first();
            
            if ($course && $equipmentType) {
                CourseRequiredEquipment::create([
                    'course_id' => $course->id,
                    'equipment_type_id' => $equipmentType->id,
                ]);
            }
        }
    }
}
