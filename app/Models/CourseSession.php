<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSession extends Model
{
    use HasFactory;

    protected $table = 'course_sessions';

    protected $fillable = [
        'academic_year_id',
        'course_id',
        'site_id',
        'room_id',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function sessionGroups(): HasMany
    {
        return $this->hasMany(SessionGroup::class, 'session_id');
    }

    public function sessionEquipment(): HasMany
    {
        return $this->hasMany(SessionEquipment::class, 'session_id');
    }
    
    /**
     * Obtenir le nom de la clé de route pour le modèle
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}
