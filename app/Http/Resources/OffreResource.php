<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Offre */
class OffreResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'duree_mois' => $this->duree_mois,
            'remuneration' => $this->remuneration,
            'date_debut' => $this->date_debut?->toIso8601String(),
            'est_disponible' => $this->est_disponible,
            'competences_count' => $this->whenCounted('competences'),
            'domaine' => $this->whenLoaded('domaine', fn () => new DomaineResource($this->domaine)),
            'competences' => $this->whenLoaded('competences', fn () => CompetenceResource::collection($this->competences)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
