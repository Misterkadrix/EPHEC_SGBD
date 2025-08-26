<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Room;
use App\Models\CourseSession;
use App\Models\Site;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            [
                'course_code' => 'INFO101',
                'site_name' => 'Campus Principal',
                'room_name' => 'Salle A101',
                'academic_year_name' => '2024-2025',
                'start_at' => '2024-09-02 09:00:00',
                'end_at' => '2024-09-02 10:00:00',
            ],
            [
                'course_code' => 'INFO101',
                'site_name' => 'Campus Principal',
                'room_name' => 'Labo Informatique B201',
                'academic_year_name' => '2024-2025',
                'start_at' => '2024-09-02 14:00:00',
                'end_at' => '2024-09-02 16:00:00',
            ],
            [
                'course_code' => 'MATH201',
                'site_name' => 'Campus Principal',
                'room_name' => 'Salle A102',
                'academic_year_name' => '2024-2025',
                'start_at' => '2024-09-03 10:00:00',
                'end_at' => '2024-09-03 11:00:00',
            ],
            [
                'course_code' => 'PROG301',
                'site_name' => 'Campus Technologique',
                'room_name' => 'Salle de Projet P301',
                'academic_year_name' => '2024-2025',
                'start_at' => '2024-09-04 13:00:00',
                'end_at' => '2024-09-04 15:00:00',
            ],
            [
                'course_code' => 'BUS101',
                'site_name' => 'Site Woluwe',
                'room_name' => 'Salle W101',
                'academic_year_name' => '2024-2025',
                'start_at' => '2024-09-05 09:00:00',
                'end_at' => '2024-09-05 10:00:00',
            ],
        ];

        foreach ($sessions as $session) {
            $course = Course::where('code', $session['course_code'])->first();
            $site = Site::where('name', $session['site_name'])->first();
            $room = Room::where('name', $session['room_name'])->first();
            $academicYear = AcademicYear::where('name', $session['academic_year_name'])->first();
            
            if ($course && $site && $room && $academicYear) {
                CourseSession::create([
                    'academic_year_id' => $academicYear->id,
                    'course_id' => $course->id,
                    'site_id' => $site->id,
                    'room_id' => $room->id,
                    'start_at' => $session['start_at'],
                    'end_at' => $session['end_at'],
                ]);
            }
        }
    }
}
