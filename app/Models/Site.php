<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'name',
        'timezone',
        'day_start',
        'day_end',
        'active_days',
    ];

    protected $casts = [
        'active_days' => 'array',
        'day_start' => 'datetime:H:i',
        'day_end' => 'datetime:H:i',
    ];

    protected $attributes = [
        'timezone' => 'Europe/Brussels',
        'day_start' => '08:00',
        'day_end' => '18:00',
        'active_days' => '["MO","TU","WE","TH","FR"]',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function courseSessions(): HasMany
    {
        return $this->hasMany(CourseSession::class);
    }

    public function courseSitePermissions(): HasMany
    {
        return $this->hasMany(CourseSitePermission::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'main_site_id');
    }
}
