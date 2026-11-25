<?php

namespace App\Models;

use Database\Factories\DomaineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domaine extends Model
{
    /** @use HasFactory<DomaineFactory> */
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
     * Un domaine contient plusieurs offres de stage.
     */
    public function offres(): HasMany
    {
        return $this->hasMany(Offre::class);
    }
}
