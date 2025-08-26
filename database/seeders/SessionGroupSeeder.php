<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\CourseSession;
use App\Models\SessionGroup;
use Illuminate\Database\Seeder;

class SessionGroupSeeder extends Seeder
{
    public function run(): void
    {
        $sessionGroups = [
            [
                'course_code' => 'INFO101',
                'group_name' => 'INFO-1A',
                'start_at' => '2024-09-02 09:00:00',
            ],
            [
                'course_code' => 'INFO101',
                'group_name' => 'INFO-1B',
                'start_at' => '2024-09-02 09:00:00',
            ],
            [
                'course_code' => 'INFO101',
                'group_name' => 'INFO-1A',
                'start_at' => '2024-09-02 14:00:00',
            ],
            [
                'course_code' => 'MATH201',
                'group_name' => 'MATH-2A',
                'start_at' => '2024-09-03 10:00:00',
            ],
            [
                'course_code' => 'PROG301',
                'group_name' => 'PROG-3A',
                'start_at' => '2024-09-04 13:00:00',
            ],
            [
                'course_code' => 'BUS101',
                'group_name' => 'BUS-1A',
                'start_at' => '2024-09-05 09:00:00',
            ],
        ];

        foreach ($sessionGroups as $sessionGroup) {
            $session = CourseSession::whereHas('course', function ($query) use ($sessionGroup) {
                $query->where('code', $sessionGroup['course_code']);
            })->where('start_at', $sessionGroup['start_at'])->first();
            
            $group = Group::where('name', $sessionGroup['group_name'])->first();
            
            if ($session && $group) {
                SessionGroup::create([
                    'session_id' => $session->id,
                    'group_id' => $group->id,
                ]);
            }
        }
    }
}
