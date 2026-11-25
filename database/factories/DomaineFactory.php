<?php

namespace Database\Factories;

use App\Models\Domaine;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Domaine>
 */
class DomaineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::ucfirst(fake('fr_FR')->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }

    /**
     * État pratique pour obtenir le domaine « Informatique ».
     */
    public function informatique(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Informatique',
            'slug' => 'informatique',
        ]);
    }
}
