<?php

namespace App\Policies;

use App\Models\Secretariat;
use App\Models\ServiceOrder;
use App\Models\User;

class ServiceOrderPolicy
{
    public function viewAny(User $user, Secretariat $secretariat): bool
    {
        return $user->isAdmin() || $user->belongsToSecretariat($secretariat->id);
    }

    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isAdmin() || $user->belongsToSecretariat($serviceOrder->secretariat_id);
    }

    public function create(User $user, Secretariat $secretariat): bool
    {
        return $user->isAdmin() || $user->belongsToSecretariat($secretariat->id);
    }

    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isAdmin() || $user->belongsToSecretariat($serviceOrder->secretariat_id);
    }

    public function delete(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isAdmin() || $user->belongsToSecretariat($serviceOrder->secretariat_id);
    }
}
