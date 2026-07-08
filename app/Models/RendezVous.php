<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';

    protected $fillable = [
        'vertical_id', 'ville', 'prenom', 'telephone', 'categorie',
        'service', 'date_rdv', 'heure_rdv', 'statut', 'montant',
    ];

    protected $casts = [
        'vertical_id' => 'integer',
        'date_rdv' => 'date',
        'heure_rdv' => 'datetime:H:i',
        'montant' => 'integer',
    ];

    public function vertical(): BelongsTo
    {
        return $this->belongsTo(Vertical::class);
    }

    /**
     * Scope : RDV non annulés pour une date et catégorie données
     */
    public function scopeActifs($query, string $date, int $categorieId)
    {
        return $query
            ->where('date_rdv', $date)
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(prestations.categorie_id, "$")) = ?', [$categorieId])
            ->join('prestations', 'rendez_vous.service', '=', 'prestations.nom')
            ->join('verticals', 'prestations.vertical_id', '=', 'verticals.id')
            ->where('rendez_vous.vertical_id', $this->vertical_id ?? Vertical::where('slug', 'beauty_salon')->value('id'))
            ->where('statut', 'not like', '%annul%');
    }
}