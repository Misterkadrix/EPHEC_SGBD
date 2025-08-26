<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'type_id');
    }

    public function courseRequiredEquipment(): HasMany
    {
        return $this->hasMany(CourseRequiredEquipment::class);
    }
}
