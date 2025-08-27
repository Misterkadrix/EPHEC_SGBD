<?php

namespace App\Contracts;

use App\Models\AcademicYear;

interface IPlanningService
{
    /**
     * Générer automatiquement les horaires pour une année académique
     */
    public function generateScheduleForAcademicYear(AcademicYear $academicYear): array;
}
