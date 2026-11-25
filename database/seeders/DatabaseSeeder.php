<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Entreprise Alpha',
            'email' => 'entreprise.alpha@example.com',
        ]);

        User::factory()->create([
            'name' => 'Entreprise Beta',
            'email' => 'entreprise.beta@example.com',
        ]);

        $this->call(DomaineSeeder::class);
        $this->call(CompetenceSeeder::class);
        $this->call(OffreSeeder::class);
    }
}
