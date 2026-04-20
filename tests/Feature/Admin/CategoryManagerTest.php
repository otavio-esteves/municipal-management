<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\CategoryManager;
use App\Models\Category;
use App\Models\Secretariat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_category_through_livewire(): void
    {
        $admin = User::factory()->create(['secretariat_id' => null]);
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create([
            'secretariat_id' => $secretariat->id,
            'name' => 'Categoria Original',
            'slug' => 'categoria-original',
        ]);

        Livewire::actingAs($admin)
            ->test(CategoryManager::class)
            ->call('create')
            ->set('name', 'Nova Categoria')
            ->set('secretariat_id', $secretariat->id)
            ->set('description', 'Descricao nova')
            ->call('store')
            ->assertHasNoErrors()
            ->assertSet('isModalOpen', false);

        $this->assertDatabaseHas('categories', [
            'name' => 'Nova Categoria',
            'slug' => 'nova-categoria',
            'secretariat_id' => $secretariat->id,
        ]);

        Livewire::actingAs($admin)
            ->test(CategoryManager::class)
            ->call('edit', $category->id)
            ->set('name', 'Categoria Atualizada')
            ->set('secretariat_id', $secretariat->id)
            ->call('store')
            ->assertHasNoErrors()
            ->assertSet('isModalOpen', false);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Categoria Atualizada',
            'slug' => 'categoria-atualizada',
        ]);

        Livewire::actingAs($admin)
            ->test(CategoryManager::class)
            ->call('delete', $category->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_manager_rejects_duplicate_slug_inside_same_secretariat(): void
    {
        $admin = User::factory()->create(['secretariat_id' => null]);
        $secretariat = Secretariat::factory()->create();

        Category::factory()->create([
            'secretariat_id' => $secretariat->id,
            'name' => 'Iluminacao Publica',
            'slug' => 'iluminacao-publica',
        ]);

        Livewire::actingAs($admin)
            ->test(CategoryManager::class)
            ->call('create')
            ->set('name', 'Iluminacao Publica')
            ->set('secretariat_id', $secretariat->id)
            ->call('store')
            ->assertHasErrors(['name'])
            ->assertSee('Este nome resulta em um slug ja existente em outra categoria.');
    }
}
