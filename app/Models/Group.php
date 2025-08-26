<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'academic_year_id',
        'name',
        'quantity',
        'main_site_id',
        'min_size',
        'max_size',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_size' => 'integer',
        'max_size' => 'integer',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function mainSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'main_site_id');
    }

    public function sessionGroups(): HasMany
    {
        return $this->hasMany(SessionGroup::class);
    }
}
