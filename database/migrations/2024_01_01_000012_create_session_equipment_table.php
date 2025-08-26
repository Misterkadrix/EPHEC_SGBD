<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_equipment', function (Blueprint $table) {
            $table->foreignId('session_id')->constrained('course_sessions')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['session_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_equipment');
    }
};
