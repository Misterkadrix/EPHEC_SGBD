<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseSitePermission;
use App\Models\Site;
use Illuminate\Database\Seeder;

class CourseSitePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'course_code' => 'INFO101',
                'site_name' => 'Campus Principal',
            ],
            [
                'course_code' => 'INFO101',
                'site_name' => 'Campus Technologique',
            ],
            [
                'course_code' => 'MATH201',
                'site_name' => 'Campus Principal',
            ],
            [
                'course_code' => 'PROG301',
                'site_name' => 'Campus Technologique',
            ],
            [
                'course_code' => 'BUS101',
                'site_name' => 'Site Woluwe',
            ],
            [
                'course_code' => 'PHYS101',
                'site_name' => 'Campus Principal',
            ],
        ];

        foreach ($permissions as $permission) {
            $course = Course::where('code', $permission['course_code'])->first();
            $site = Site::where('name', $permission['site_name'])->first();
            
            if ($course && $site) {
                CourseSitePermission::create([
                    'course_id' => $course->id,
                    'site_id' => $site->id,
                ]);
            }
        }
    }
}
