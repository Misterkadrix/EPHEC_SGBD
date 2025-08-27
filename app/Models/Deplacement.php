<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deplacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_depart_id',
        'site_depart_id',
        'room_depart_id',
        'session_arrivee_id',
        'site_arrivee_id',
        'room_arrivee_id',
        'group_id',
        'heure_depart',
        'heure_arrivee',
        'duree_trajet_minutes',
    ];

    protected $casts = [
        'heure_depart' => 'datetime',
        'heure_arrivee' => 'datetime',
        'duree_trajet_minutes' => 'integer',
    ];

    // Relations avec les sessions
    public function sessionDepart(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'session_depart_id');
    }

    public function sessionArrivee(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'session_arrivee_id');
    }

    // Relations avec les sites
    public function siteDepart(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_depart_id');
    }

    public function siteArrivee(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_arrivee_id');
    }

    // Relations avec les salles
    public function roomDepart(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_depart_id');
    }

    public function roomArrivee(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_arrivee_id');
    }

    // Relation avec le groupe
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // Accesseurs utiles
    public function getDateAttribute()
    {
        return $this->heure_depart->toDateString();
    }

    public function getHeureDepartFormattedAttribute()
    {
        return $this->heure_depart->format('H:i');
    }

    public function getHeureArriveeFormattedAttribute()
    {
        return $this->heure_arrivee->format('H:i');
    }

    public function getDureeTrajetFormattedAttribute()
    {
        $heures = intval($this->duree_trajet_minutes / 60);
        $minutes = $this->duree_trajet_minutes % 60;
        
        if ($heures > 0) {
            return "{$heures}h{$minutes}min";
        }
        
        return "{$minutes}min";
    }

    // Scopes utiles
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('heure_depart', $date);
    }

    public function scopeForGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeInterSite($query)
    {
        return $query->where('site_depart_id', '!=', 'site_arrivee_id');
    }
}
