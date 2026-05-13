<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Claim $claim): bool
    {
        return $user->isAdmin()
            || $claim->claimant_id === $user->id
            || $claim->report?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function approve(User $user, Claim $claim): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Claim $claim): bool
    {
        return $user->isAdmin();
    }
}
