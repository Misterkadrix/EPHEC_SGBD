<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'type_id',
        'is_mobile',
        'fixed_room_id',
    ];

    protected $casts = [
        'is_mobile' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function fixedRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'fixed_room_id');
    }

    public function sessionEquipment(): HasMany
    {
        return $this->hasMany(SessionEquipment::class);
    }
}
