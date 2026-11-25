<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competence_offre', function (Blueprint $table) {
            $table->foreignId('competence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offre_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['competence_id', 'offre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competence_offre');
    }
};
