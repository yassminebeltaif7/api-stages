<?php

namespace Database\Factories;

use App\Models\Domaine;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offre>
 */
class OffreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'domaine_id' => Domaine::factory(),
            'titre' => fake('fr_FR')->sentence(4),
            'description' => fake('fr_FR')->paragraph,
            'duree_mois' => fake()->numberBetween(1, 6),
            'remuneration' => fake()->numberBetween(0, 2500),
            'date_debut' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'est_disponible' => true,
        ];
    }

    /**
     * L'offre est ouverte aux étudiants.
     */
    public function disponible(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_disponible' => true,
        ]);
    }

    /**
     * L'offre n'est plus ouverte.
     */
    public function indisponible(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_disponible' => false,
        ]);
    }
}
