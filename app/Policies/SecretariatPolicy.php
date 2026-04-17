<?php

namespace App\Policies;

use App\Models\Secretariat;
use App\Models\User;

class SecretariatPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Secretariat $secretariat): bool
    {
        return $user->isAdmin() || $user->belongsToSecretariat($secretariat->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Secretariat $secretariat): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Secretariat $secretariat): bool
    {
        return $user->isAdmin();
    }
}
