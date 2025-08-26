<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name', 80);
            $table->integer('quantity');
            $table->foreignId('main_site_id')->constrained('sites')->onDelete('cascade');
            $table->integer('min_size')->default(20);
            $table->integer('max_size')->default(40);
            $table->timestamps();

            $table->index(['academic_year_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
