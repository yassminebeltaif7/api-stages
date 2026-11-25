<?php

namespace Database\Seeders;

use App\Models\Competence;
use App\Models\Domaine;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OffreSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = User::pluck('id')->all();
        $domaines = Domaine::pluck('id')->all();
        $competences = Competence::pluck('id')->all();

        foreach (range(1, 20) as $index) {
            $offre = Offre::factory()->create([
                'user_id' => $users[$index % count($users)],
                'domaine_id' => $domaines[$index % count($domaines)],
            ]);

            $offre->competences()->attach(
                collect($competences)->random(2 + ($index % 2))->all()
            );
        }
    }
}
