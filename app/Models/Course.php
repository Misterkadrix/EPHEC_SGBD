<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'code',
        'title',
        'description',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function courseSessions(): HasMany
    {
        return $this->hasMany(CourseSession::class);
    }

    public function courseSitePermissions(): HasMany
    {
        return $this->hasMany(CourseSitePermission::class);
    }

    public function courseRequiredEquipment(): HasMany
    {
        return $this->hasMany(CourseRequiredEquipment::class);
    }

    /**
     * Les groupes associés à ce cours
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_course')
                    ->withTimestamps();
    }
}
