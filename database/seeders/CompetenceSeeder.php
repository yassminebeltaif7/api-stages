<?php

namespace Database\Seeders;

use App\Models\Competence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetenceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Competence::factory()->createMany([
            [
                'name' => 'PHP',
                'slug' => 'php',
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
            ],
            [
                'name' => 'SQL',
                'slug' => 'sql',
            ],
            [
                'name' => 'Git',
                'slug' => 'git',
            ],
            [
                'name' => 'API REST',
                'slug' => 'api-rest',
            ],
        ]);
    }
}
