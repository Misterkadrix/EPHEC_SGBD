<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionEquipment extends Model
{
    use HasFactory;

    protected $table = 'session_equipment';

    protected $fillable = [
        'session_id',
        'equipment_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
