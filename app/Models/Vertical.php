<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vertical extends Model
{
    protected $fillable = [
        'slug', 'nom', 'ville', 'ouverture', 'fermeture',
        'capacites_par_categorie', 'categories', 'devise',
        'booking_api_secret', 'escalation_phone', 'actif',
    ];

    protected $casts = [
        'ouverture' => 'datetime:H:i',
        'fermeture' => 'datetime:H:i',
        'capacites_par_categorie' => 'array',
        'categories' => 'array',
        'actif' => 'boolean',
    ];

    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    /**
     * Récupère une verticale active par son slug
     */
    public static function getBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->where('actif', true)->first();
    }

    /**
     * Vérifie une clé API contre cette verticale
     */
    public function verifieCleApi(?string $cle): bool
    {
        return $cle !== null && hash_equals($this->booking_api_secret, $cle);
    }
}