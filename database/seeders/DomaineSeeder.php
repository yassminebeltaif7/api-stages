<?php

namespace Database\Seeders;

use App\Models\Domaine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomaineSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Domaine::factory()->createMany([
            [
                'name' => 'Développement web',
                'slug' => 'developpement-web',
            ],
            [
                'name' => 'Data et intelligence artificielle',
                'slug' => 'data-intelligence-artificielle',
            ],
            [
                'name' => 'Cybersécurité',
                'slug' => 'cybersecurite',
            ],
        ]);
    }
}
