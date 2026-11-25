<?php

namespace App\Models;

use Database\Factories\CompetenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competence extends Model
{
    /** @use HasFactory<CompetenceFactory> */
    use HasFactory;

    /**
     * Les seuls champs que l'on peut remplir depuis le code.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Conversion automatique des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    /**
     * Une compétence apparaît dans plusieurs offres.
     */
    public function offres(): BelongsToMany
    {
        return $this->belongsToMany(Offre::class, 'competence_offre')
            ->withTimestamps();
    }
}
