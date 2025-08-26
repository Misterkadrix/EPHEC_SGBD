<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionGroup extends Model
{
    use HasFactory;

    protected $table = 'session_groups';

    protected $fillable = [
        'session_id',
        'group_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
