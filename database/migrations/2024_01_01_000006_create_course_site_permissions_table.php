<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_site_permissions', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['course_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_site_permissions');
    }
};
