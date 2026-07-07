<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestation extends Model
{
    protected $table = 'prestations';

    protected $fillable = [
        'vertical_id', 'nom', 'categorie_id', 'prix', 'duree_minutes',
    ];

    protected $casts = [
        'vertical_id' => 'integer',
        'categorie_id' => 'integer',
        'duree_minutes' => 'integer',
    ];

    public function vertical(): BelongsTo
    {
        return $this->belongsTo(Vertical::class);
    }
}