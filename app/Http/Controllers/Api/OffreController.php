<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexOffreRequest;
use App\Http\Requests\StoreOffreRequest;
use App\Http\Requests\UpdateOffreRequest;
use App\Http\Resources\OffreResource;
use App\Models\Domaine;
use App\Models\Offre;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class OffreController extends Controller
{
    private const RELATIONS = [
        'domaine:id,name,slug',
        'competences:id,name,slug',
    ];

    /**
     * Liste paginée des offres, avec filtre optionnel par slug du domaine.
     */
    public function index(IndexOffreRequest $request)
    {
        $query = Offre::query()
            ->with(self::RELATIONS)
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
        return new OffreResource($offre->load(self::RELATIONS));
    }

    /**
     * Création d'une offre appartenant à l'utilisateur connecté.
     */
    public function store(StoreOffreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $offre = $request->user()->offres()->create([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'duree_mois' => $data['duree_mois'],
            'remuneration' => $data['remuneration'],
            'date_debut' => $data['date_debut'],
            'est_disponible' => $data['est_disponible'] ?? true,
            'domaine_id' => $data['domaine_id'],
        ]);

        $offre->competences()->sync($data['competences']);

        return (new OffreResource($offre->load(self::RELATIONS)))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Modification d'une offre, autorisée par UpdateOffreRequest + Policy.
     */
    public function update(UpdateOffreRequest $request, Offre $offre): OffreResource
    {
        $data = $request->validated();

        $offre->fill(Arr::except($data, ['competences']))->save();

        if (array_key_exists('competences', $data)) {
            $offre->competences()->sync($data['competences']);
        }

        return new OffreResource($offre->load(self::RELATIONS));
    }

    /**
     * Suppression d'une offre, autorisée par la Policy.
     */
    public function destroy(Offre $offre): JsonResponse
    {
        Gate::authorize('delete', $offre);

        $offre->delete();

        return response()->json(['message' => 'Offre supprimée.']);
    }
}
