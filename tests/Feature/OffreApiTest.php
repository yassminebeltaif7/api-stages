<?php

use App\Models\Competence;
use App\Models\Domaine;
use App\Models\Offre;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('renvoie la liste paginée des offres', function () {
    Offre::factory()->count(3)->create();

    $this->getJson('/api/offres')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'titre',
                    'description',
                    'duree_mois',
                    'remuneration',
                    'date_debut',
                    'est_disponible',
                ],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
});

it('filtre les offres par slug du domaine', function () {
    $web = Domaine::factory()->create(['slug' => 'developpement-web']);
    $data = Domaine::factory()->create(['slug' => 'data-intelligence-artificielle']);

    Offre::factory()->count(2)->create(['domaine_id' => $web->id]);
    Offre::factory()->create(['domaine_id' => $data->id]);

    $this->getJson('/api/offres?domaine=developpement-web')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.domaine.slug', 'developpement-web');
});

it('refuse la création d’une offre sans jeton', function () {
    $this->postJson('/api/offres', [])
        ->assertUnauthorized();
});

it('refuse la modification d’une offre appartenant à une autre entreprise', function () {
    $proprietaire = User::factory()->create();
    $autreEntreprise = User::factory()->create();
    $offre = Offre::factory()->create(['user_id' => $proprietaire->id]);

    Sanctum::actingAs($autreEntreprise);

    $this->putJson("/api/offres/{$offre->id}", ['titre' => 'Titre interdit'])
        ->assertForbidden();
});

it('refuse une création avec une durée invalide', function () {
    $domaine = Domaine::factory()->create();
    $competence = Competence::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/offres', [
        'titre' => 'Stage invalide',
        'description' => 'Description de test.',
        'duree_mois' => 0,
        'remuneration' => 800,
        'date_debut' => '2026-12-01',
        'domaine_id' => $domaine->id,
        'competences' => [$competence->id],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('duree_mois');
});

it('crée une offre et enregistre ses compétences', function () {
    $entreprise = User::factory()->create();
    $domaine = Domaine::factory()->create();
    $php = Competence::factory()->create(['slug' => 'php']);
    $laravel = Competence::factory()->create(['slug' => 'laravel']);

    Sanctum::actingAs($entreprise);

    $reponse = $this->postJson('/api/offres', [
        'titre' => 'Stage Pest',
        'description' => 'Description de test.',
        'duree_mois' => 6,
        'remuneration' => 1000,
        'date_debut' => '2026-12-01',
        'est_disponible' => true,
        'domaine_id' => $domaine->id,
        'competences' => [$php->id, $laravel->id],
    ]);

    $reponse->assertCreated()
        ->assertJsonPath('data.titre', 'Stage Pest');

    $this->assertDatabaseHas('offres', [
        'user_id' => $entreprise->id,
        'titre' => 'Stage Pest',
        'duree_mois' => 6,
    ]);

    $this->assertDatabaseHas('competence_offre', [
        'offre_id' => $reponse->json('data.id'),
        'competence_id' => $php->id,
    ]);

    $this->assertDatabaseHas('competence_offre', [
        'offre_id' => $reponse->json('data.id'),
        'competence_id' => $laravel->id,
    ]);
});
