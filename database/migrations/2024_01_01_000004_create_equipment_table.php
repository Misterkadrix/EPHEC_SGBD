<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_id')->constrained('equipment_types')->onDelete('cascade');
            $table->boolean('is_mobile');
            $table->foreignId('fixed_room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->timestamps();

            $table->index(['site_id', 'type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
