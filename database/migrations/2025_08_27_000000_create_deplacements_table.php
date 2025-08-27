<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deplacements', function (Blueprint $table) {
            $table->id();
            
            // Session de départ (cours qui se termine)
            $table->foreignId('session_depart_id')->constrained('course_sessions')->onDelete('cascade');
            $table->foreignId('site_depart_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('room_depart_id')->constrained('rooms')->onDelete('cascade');
            
            // Session d'arrivée (cours qui commence)
            $table->foreignId('session_arrivee_id')->constrained('course_sessions')->onDelete('cascade');
            $table->foreignId('site_arrivee_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('room_arrivee_id')->constrained('rooms')->onDelete('cascade');
            
            // Groupe concerné par le déplacement
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            
            // Informations temporelles
            $table->dateTime('heure_depart'); // Heure de fin du cours de départ
            $table->dateTime('heure_arrivee'); // Heure de début du cours d'arrivée
            $table->integer('duree_trajet_minutes'); // Durée du trajet en minutes
            
            // Horodatage
            $table->timestamps();
            
            // Index pour performance
            $table->index(['heure_depart', 'group_id']);
            $table->index(['site_depart_id', 'site_arrivee_id']);
            $table->index(['heure_depart', 'heure_arrivee']);
            
            // Contraintes d'unicité
            $table->unique(['session_depart_id', 'session_arrivee_id', 'group_id'], 'deplacements_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deplacements');
    }
};
