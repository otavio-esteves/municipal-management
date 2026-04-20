<?php

namespace App\Application\Auth;

use App\Application\Auth\Data\RedirectTargetData;
use App\Models\User;

class ResolveUserHomeRoute
{
    public function handle(User $user): RedirectTargetData
    {
        if ($user->secretariat_id !== null) {
            return new RedirectTargetData(
                routeName: 'secretariats.ods',
                parameters: ['secretariat' => $user->secretariat_id],
            );
        }

        return new RedirectTargetData('dashboard');
    }
}
