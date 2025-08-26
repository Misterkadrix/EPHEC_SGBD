<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_groups', function (Blueprint $table) {
            $table->foreignId('session_id')->constrained('course_sessions')->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['session_id', 'group_id']);
            $table->index(['group_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_groups');
    }
};
