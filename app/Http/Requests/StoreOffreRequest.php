<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOffreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'duree_mois' => ['required', 'integer', 'min:1', 'max:12'],
            'remuneration' => ['required', 'numeric', 'min:0'],
            'date_debut' => ['required', 'date'],
            'est_disponible' => ['sometimes', 'boolean'],
            'domaine_id' => ['required', 'exists:domaines,id'],
            'competences' => ['required', 'array', 'min:1'],
            'competences.*' => ['exists:competences,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'duree_mois.required' => 'La durée en mois est obligatoire.',
            'duree_mois.integer' => 'La durée doit être un nombre entier de mois.',
            'duree_mois.min' => 'La durée doit être d’au moins 1 mois.',
            'remuneration.required' => 'La rémunération est obligatoire.',
            'remuneration.numeric' => 'La rémunération doit être un nombre.',
            'remuneration.min' => 'La rémunération ne peut pas être négative.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'domaine_id.required' => 'Le domaine est obligatoire.',
            'domaine_id.exists' => 'Le domaine choisi n’existe pas.',
            'competences.required' => 'Au moins une compétence est obligatoire.',
            'competences.array' => 'Les compétences doivent être une liste.',
            'competences.min' => 'Au moins une compétence est obligatoire.',
            'competences.*.exists' => 'Une compétence choisie n’existe pas.',
        ];
    }
}
