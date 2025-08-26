<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_at', 6);
            $table->dateTime('end_at', 6);
            $table->timestamps();

            $table->unique(['room_id', 'start_at']);
            $table->index(['academic_year_id', 'start_at']);
            $table->index(['site_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
    }
};
