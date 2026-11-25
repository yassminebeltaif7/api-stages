<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexOffreRequest;
use App\Http\Resources\OffreResource;
use App\Models\Domaine;
use App\Models\Offre;

class OffreController extends Controller
{
    /**
     * Liste paginée des offres, avec filtre optionnel par slug du domaine.
     */
    public function index(IndexOffreRequest $request)
    {
        $query = Offre::query()
            ->with([
                'domaine:id,name,slug',
                'competences:id,name,slug',
            ])
            ->withCount('competences');

        $slug = $request->validated()['domaine'] ?? null;

        if ($slug !== null) {
            $domaine = Domaine::where('slug', $slug)->firstOrFail();
            $query->where('domaine_id', $domaine->id);
        }

        return OffreResource::collection($query->latest()->paginate(10));
    }

    /**
     * Détail d'une offre.
     */
    public function show(Offre $offre): OffreResource
    {
        $offre->load([
            'domaine:id,name,slug',
            'competences:id,name,slug',
        ]);

        return new OffreResource($offre);
    }
}
