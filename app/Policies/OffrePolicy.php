<?php

namespace App\Policies;

use App\Models\Offre;
use App\Models\User;

class OffrePolicy
{
    /**
     * Seule l'entreprise propriétaire peut modifier son offre.
     */
    public function update(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }

    /**
     * Seule l'entreprise propriétaire peut supprimer son offre.
     */
    public function delete(User $user, Offre $offre): bool
    {
        return $user->id === $offre->user_id;
    }
}
