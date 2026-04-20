<?php

namespace Tests\Unit\Auth;

use App\Application\Auth\ResolveUserHomeRoute;
use App\Models\Secretariat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResolveUserHomeRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_resolved_to_dashboard_route(): void
    {
        $user = User::factory()->admin()->create();

        $target = app(ResolveUserHomeRoute::class)->handle($user);

        $this->assertSame('dashboard', $target->routeName);
        $this->assertSame([], $target->parameters);
    }

    public function test_secretariat_user_is_resolved_to_own_service_order_route(): void
    {
        $secretariat = Secretariat::factory()->create();
        $user = User::factory()->forSecretariat($secretariat)->create();

        $target = app(ResolveUserHomeRoute::class)->handle($user);

        $this->assertSame('secretariats.ods', $target->routeName);
        $this->assertSame(['secretariat' => $secretariat->id], $target->parameters);
    }

    public function test_user_without_secretariat_receives_predictable_dashboard_route(): void
    {
        $user = User::factory()->create(['secretariat_id' => null]);

        $target = app(ResolveUserHomeRoute::class)->handle($user);

        $this->assertSame('dashboard', $target->routeName);
        $this->assertSame([], $target->parameters);
    }
}
