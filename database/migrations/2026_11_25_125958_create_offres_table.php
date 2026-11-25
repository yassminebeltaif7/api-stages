<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('domaine_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('description');
            $table->unsignedInteger('duree_mois');
            $table->decimal('remuneration', 8, 2);
            $table->date('date_debut');
            $table->boolean('est_disponible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
