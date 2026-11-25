<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DomaineResource;
use App\Models\Domaine;

class DomaineController extends Controller
{
    /**
     * Liste paginée des domaines, avec le nombre d'offres.
     */
    public function index()
    {
        return DomaineResource::collection(
            Domaine::withCount('offres')->latest()->paginate(10)
        );
    }

    /**
     * Détail d'un domaine.
     */
    public function show(Domaine $domaine): DomaineResource
    {
        return new DomaineResource($domaine->loadCount('offres'));
    }
}
