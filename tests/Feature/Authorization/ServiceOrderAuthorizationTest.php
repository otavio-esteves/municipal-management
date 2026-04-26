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

    public function test_secretariat_user_can_edit_and_delete_own_service_order(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $user = User::factory()->create(['secretariat_id' => $secretariat->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'secretariat_id' => $secretariat->id,
            'category_id' => $category->id,
            'title' => 'ODS original',
        ]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $secretariat])
            ->call('edit', $serviceOrder->id)
            ->assertSet('odsId', $serviceOrder->id)
            ->set('title', 'ODS atualizada')
            ->set('categoryId', $category->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'title' => 'ODS atualizada',
            'secretariat_id' => $secretariat->id,
        ]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $secretariat])
            ->call('delete', $serviceOrder->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('service_orders', [
            'id' => $serviceOrder->id,
            'secretariat_id' => $secretariat->id,
        ]);
    }

    public function test_checklist_changes_are_saved_when_closing_edit_modal(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $user = User::factory()->create(['secretariat_id' => $secretariat->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'secretariat_id' => $secretariat->id,
            'category_id' => $category->id,
            'title' => 'ODS original',
        ]);

        $serviceOrder->checklistItems()->create([
            'label' => 'Item existente',
            'is_completed' => false,
            'sort_order' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $secretariat])
            ->call('edit', $serviceOrder->id, 'checklist')
            ->set('title', 'Titulo nao salvo')
            ->set('newChecklistItem', 'Nova etapa automatica')
            ->call('closeModal')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ods_checklists', [
            'service_order_id' => $serviceOrder->id,
            'label' => 'Nova etapa automatica',
            'is_completed' => false,
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'title' => 'ODS original',
        ]);

        $this->assertDatabaseMissing('service_orders', [
            'id' => $serviceOrder->id,
            'title' => 'Titulo nao salvo',
        ]);
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

    public function test_secretariat_user_cannot_delete_service_order_from_another_secretariat(): void
    {
        $ownSecretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $user = User::factory()->create(['secretariat_id' => $ownSecretariat->id]);
        $serviceOrder = ServiceOrder::factory()->forSecretariat($otherSecretariat)->create();

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $ownSecretariat])
            ->call('delete', $serviceOrder->id)
            ->assertSee('Ordem de servico nao encontrada para esta secretaria.');

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'secretariat_id' => $otherSecretariat->id,
            'deleted_at' => null,
        ]);
    }

    public function test_secretariat_user_cannot_update_service_order_from_another_secretariat_by_tampering_with_id(): void
    {
        $ownSecretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $ownCategory = Category::factory()->create(['secretariat_id' => $ownSecretariat->id]);
        $user = User::factory()->create(['secretariat_id' => $ownSecretariat->id]);
        $serviceOrder = ServiceOrder::factory()->forSecretariat($otherSecretariat)->create([
            'title' => 'ODS externa',
        ]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $ownSecretariat])
            ->set('odsId', $serviceOrder->id)
            ->set('title', 'Tentativa de invasao')
            ->set('categoryId', $ownCategory->id)
            ->call('save')
            ->assertSee('Ordem de servico nao encontrada para esta secretaria.');

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'title' => 'ODS externa',
            'secretariat_id' => $otherSecretariat->id,
        ]);
    }

    public function test_secretariat_user_can_change_status_of_own_service_order(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $user = User::factory()->create(['secretariat_id' => $secretariat->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'secretariat_id' => $secretariat->id,
            'category_id' => $category->id,
            'status' => \App\Domain\ServiceOrders\ServiceOrderStatus::Pending,
        ]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $secretariat])
            ->call('updateStatus', $serviceOrder->id, \App\Domain\ServiceOrders\ServiceOrderStatus::InProgress->value)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'status' => \App\Domain\ServiceOrders\ServiceOrderStatus::InProgress->value,
        ]);
    }

    public function test_secretariat_user_cannot_change_status_of_service_order_from_another_secretariat(): void
    {
        $ownSecretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $user = User::factory()->create(['secretariat_id' => $ownSecretariat->id]);
        $serviceOrder = ServiceOrder::factory()->forSecretariat($otherSecretariat)->create([
            'status' => \App\Domain\ServiceOrders\ServiceOrderStatus::Pending,
        ]);

        Livewire::actingAs($user)
            ->test(ServiceOrderManager::class, ['secretariat' => $ownSecretariat])
            ->call('updateStatus', $serviceOrder->id, \App\Domain\ServiceOrders\ServiceOrderStatus::Completed->value)
            ->assertSee('Ordem de servico nao encontrada para esta secretaria.');

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'secretariat_id' => $otherSecretariat->id,
            'status' => \App\Domain\ServiceOrders\ServiceOrderStatus::Pending->value,
        ]);
    }

    public function test_admin_can_manage_service_orders_for_any_secretariat(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $admin = User::factory()->create(['secretariat_id' => null]);
        $serviceOrder = ServiceOrder::factory()->create([
            'secretariat_id' => $secretariat->id,
            'category_id' => $category->id,
            'title' => 'ODS inicial',
        ]);

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

        Livewire::actingAs($admin)
            ->test(ServiceOrderManager::class, ['secretariat' => $secretariat])
            ->call('edit', $serviceOrder->id)
            ->assertSet('odsId', $serviceOrder->id)
            ->set('title', 'ODS editada pelo admin')
            ->set('categoryId', $category->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'title' => 'ODS editada pelo admin',
        ]);

        Livewire::actingAs($admin)
            ->test(ServiceOrderManager::class, ['secretariat' => $secretariat])
            ->call('delete', $serviceOrder->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('service_orders', [
            'id' => $serviceOrder->id,
        ]);
    }
}
