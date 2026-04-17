<?php

namespace Tests\Feature\Authorization;

use App\Livewire\Secretariat\ServiceOrderManager;
use App\Models\Category;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceOrderAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_secretariat_user_cannot_edit_service_order_from_another_secretariat(): void
    {
        $ownSecretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $user = User::factory()->create(['secretariat_id' => $ownSecretariat->id]);
        $serviceOrder = ServiceOrder::factory()->forSecretariat($otherSecretariat)->create();

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $ownSecretariat])
            ->call('edit', $serviceOrder->id)
            ->assertSee('Ordem de servico nao encontrada para esta secretaria.');
    }

    public function test_secretariat_user_cannot_create_service_order_with_category_from_other_secretariat(): void
    {
        $ownSecretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $ownCategory = Category::factory()->create(['secretariat_id' => $ownSecretariat->id]);
        $otherCategory = Category::factory()->create(['secretariat_id' => $otherSecretariat->id]);
        $user = User::factory()->create(['secretariat_id' => $ownSecretariat->id]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $ownSecretariat])
            ->set('title', 'Nova ODS')
            ->set('categoryId', $otherCategory->id)
            ->call('save')
            ->assertSee('A categoria selecionada nao pertence a esta secretaria.');

        $this->assertDatabaseMissing('service_orders', [
            'title' => 'Nova ODS',
            'category_id' => $otherCategory->id,
        ]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $ownSecretariat])
            ->set('title', 'ODS valida')
            ->set('categoryId', $ownCategory->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('service_orders', [
            'title' => 'ODS valida',
            'secretariat_id' => $ownSecretariat->id,
            'category_id' => $ownCategory->id,
        ]);
    }

    public function test_secretariat_user_cannot_mount_component_for_other_secretariat(): void
    {
        $ownSecretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $user = User::factory()->create(['secretariat_id' => $ownSecretariat->id]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $otherSecretariat])
            ->assertForbidden();
    }

    public function test_admin_can_manage_service_orders_for_any_secretariat(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $admin = User::factory()->create(['secretariat_id' => null]);

        Livewire::actingAs($admin)
            ->test(ServiceOrderManager::class, ['secretariat' => $secretariat])
            ->set('title', 'ODS do admin')
            ->set('categoryId', $category->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('service_orders', [
            'title' => 'ODS do admin',
            'secretariat_id' => $secretariat->id,
            'category_id' => $category->id,
        ]);
    }
}
