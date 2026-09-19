<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class PerformancePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Solo gli amministratori possono aggiungere nuove esibizioni alla programmazione.');
    }
}
