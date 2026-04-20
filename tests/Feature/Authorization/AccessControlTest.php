<?php

namespace Tests\Feature\Authorization;

use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\SecretariatManager;
use App\Models\Secretariat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_protected_routes(): void
    {
        $secretariat = Secretariat::factory()->create();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.secretariats'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.categories'))
            ->assertRedirect(route('login'));

        $this->get(route('secretariats.ods', $secretariat))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['secretariat_id' => null]);

        $this->actingAs($admin)
            ->get(route('admin.secretariats'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.categories'))
            ->assertOk();
    }

    public function test_secretariat_user_cannot_access_admin_routes(): void
    {
        $secretariat = Secretariat::factory()->create();
        $user = User::factory()->create(['secretariat_id' => $secretariat->id]);

        $this->actingAs($user)
            ->get(route('admin.secretariats'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.categories'))
            ->assertForbidden();
    }

    public function test_secretariat_user_cannot_mount_admin_livewire_components(): void
    {
        $secretariat = Secretariat::factory()->create();
        $user = User::factory()->create(['secretariat_id' => $secretariat->id]);

        Livewire::actingAs($user)
            ->test(SecretariatManager::class)
            ->assertForbidden();

        Livewire::actingAs($user)
            ->test(CategoryManager::class)
            ->assertForbidden();
    }

    public function test_secretariat_user_can_access_only_own_service_order_panel(): void
    {
        $ownSecretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $user = User::factory()->create(['secretariat_id' => $ownSecretariat->id]);

        $this->actingAs($user)
            ->get(route('secretariats.ods', $ownSecretariat))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('secretariats.ods', $otherSecretariat))
            ->assertForbidden();
    }

    public function test_admin_can_access_any_service_order_panel(): void
    {
        $admin = User::factory()->create(['secretariat_id' => null]);
        $secretariat = Secretariat::factory()->create();

        $this->actingAs($admin)
            ->get(route('secretariats.ods', $secretariat))
            ->assertOk();
    }
}
