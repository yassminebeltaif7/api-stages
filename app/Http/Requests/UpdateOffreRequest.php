<?php

namespace App\Http\Requests;

use App\Models\Offre;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOffreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $offre = $this->route('offre');

        return $offre instanceof Offre
            && $this->user() !== null
            && $this->user()->can('update', $offre);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titre' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'duree_mois' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'remuneration' => ['sometimes', 'numeric', 'min:0'],
            'date_debut' => ['sometimes', 'date'],
            'est_disponible' => ['sometimes', 'boolean'],
            'domaine_id' => ['sometimes', 'exists:domaines,id'],
            'competences' => ['sometimes', 'array', 'min:1'],
            'competences.*' => ['exists:competences,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'duree_mois.integer' => 'La durée doit être un nombre entier de mois.',
            'duree_mois.min' => 'La durée doit être d’au moins 1 mois.',
            'remuneration.numeric' => 'La rémunération doit être un nombre.',
            'remuneration.min' => 'La rémunération ne peut pas être négative.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'domaine_id.exists' => 'Le domaine choisi n’existe pas.',
            'competences.array' => 'Les compétences doivent être une liste.',
            'competences.min' => 'Au moins une compétence est obligatoire.',
            'competences.*.exists' => 'Une compétence choisie n’existe pas.',
        ];
    }
}
