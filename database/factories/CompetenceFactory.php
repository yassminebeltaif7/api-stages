<?php

namespace Database\Factories;

use App\Models\Competence;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Competence>
 */
class CompetenceFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::ucfirst(fake('fr_FR')->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }

    /**
     * État pratique pour obtenir la compétence « PHP ».
     */
    public function php(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'PHP',
            'slug' => 'php',
        ]);
    }
}
