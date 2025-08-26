<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->string('name', 120);
            $table->string('timezone', 64)->default('Europe/Brussels');
            $table->time('day_start')->default('08:00');
            $table->time('day_end')->default('18:00');
            $table->json('active_days');
            $table->timestamps();

            $table->index(['university_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
