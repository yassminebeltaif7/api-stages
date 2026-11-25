<?php

namespace App\Models;

use Database\Factories\OffreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offre extends Model
{
    /** @use HasFactory<OffreFactory> */
    use HasFactory;

    /**
     * Les seuls champs que l'on peut remplir depuis le code.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'domaine_id',
        'titre',
        'description',
        'duree_mois',
        'remuneration',
        'date_debut',
        'est_disponible',
    ];

    /**
     * Conversion automatique des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duree_mois' => 'integer',
            'remuneration' => 'decimal:2',
            'date_debut' => 'date',
            'est_disponible' => 'boolean',
        ];
    }

    /**
     * Une offre appartient à une entreprise.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une offre appartient à un domaine.
     */
    public function domaine(): BelongsTo
    {
        return $this->belongsTo(Domaine::class);
    }

    /**
     * Une offre peut demander plusieurs compétences.
     */
    public function competences(): BelongsToMany
    {
        return $this->belongsToMany(Competence::class, 'competence_offre')
            ->withTimestamps();
    }
}
