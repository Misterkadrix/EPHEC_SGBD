<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            // 1. Utilisateur de base
            UserSeeder::class,
            
            // 2. Structure de base
            UniversitySeeder::class,
            SiteSeeder::class,
            RoomSeeder::class,
            EquipmentTypeSeeder::class,
            EquipmentSeeder::class,
            
            // 3. Cours et permissions
            CourseSeeder::class,
            CourseSitePermissionSeeder::class,
            CourseRequiredEquipmentSeeder::class,
            
            // 4. Années académiques et groupes
            AcademicYearSeeder::class,
            GroupSeeder::class,
            
            // 5. Associations et permissions
            GroupCourseSeeder::class,
            
            // 6. Fériés
            HolidaySeeder::class,
            
            // 7. Déplacements
            DeplacementSeeder::class,
            
            // 8. Ancien seeder de produits (à supprimer plus tard)
            ProductSeeder::class,
        ]);
    }
}
