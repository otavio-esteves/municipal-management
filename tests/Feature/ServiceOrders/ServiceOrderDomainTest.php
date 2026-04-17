<?php

namespace Tests\Feature\ServiceOrders;

use App\Application\ServiceOrders\CreateServiceOrder;
use App\Application\ServiceOrders\Data\ServiceOrderData;
use App\Application\ServiceOrders\UpdateServiceOrder;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderCategory;
use App\Domain\ServiceOrders\Exceptions\InvalidServiceOrderStatusTransition;
use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Models\Category;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOrderDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_order_code_is_generated_from_persisted_id(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $data = ServiceOrderData::fromArray([
            'title' => 'ODS 1',
            'location' => 'Rua A',
            'category_id' => $category->id,
            'due_date' => null,
            'is_urgent' => false,
            'observation' => null,
        ]);

        $first = app(CreateServiceOrder::class)->handle($secretariat->id, $data);
        $second = app(CreateServiceOrder::class)->handle($secretariat->id, ServiceOrderData::fromArray([
            'title' => 'ODS 2',
            'location' => 'Rua B',
            'category_id' => $category->id,
            'due_date' => null,
            'is_urgent' => true,
            'observation' => null,
        ]));

        $this->assertSame(ServiceOrderStatus::Pending, $first->status);
        $this->assertMatchesRegularExpression('/^ODS-\d{6}$/', $first->code);
        $this->assertSame(ServiceOrder::codeFromId($first->id), $first->code);
        $this->assertSame(ServiceOrder::codeFromId($second->id), $second->code);
        $this->assertNotSame($first->code, $second->code);
    }

    public function test_update_service_order_preserves_existing_status(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $serviceOrder = ServiceOrder::factory()
            ->forSecretariat($secretariat)
            ->create([
                'category_id' => $category->id,
                'status' => ServiceOrderStatus::InProgress,
            ]);

        $updated = app(UpdateServiceOrder::class)->handle(
            $secretariat->id,
            $serviceOrder->id,
            ServiceOrderData::fromArray([
                'title' => 'Titulo atualizado',
                'location' => 'Rua Atualizada',
                'category_id' => $category->id,
                'due_date' => '2026-05-01',
                'is_urgent' => true,
                'observation' => 'Obs atualizada',
            ]),
        );

        $this->assertSame(ServiceOrderStatus::InProgress, $updated->status);
        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrder->id,
            'status' => ServiceOrderStatus::InProgress->value,
        ]);
    }

    public function test_create_service_order_use_case_rejects_category_from_other_secretariat(): void
    {
        $secretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $foreignCategory = Category::factory()->create(['secretariat_id' => $otherSecretariat->id]);

        $this->expectException(InvalidServiceOrderCategory::class);

        app(CreateServiceOrder::class)->handle(
            $secretariat->id,
            ServiceOrderData::fromArray([
                'title' => 'ODS invalida',
                'location' => 'Rua X',
                'category_id' => $foreignCategory->id,
                'due_date' => null,
                'is_urgent' => false,
                'observation' => null,
            ]),
        );
    }

    public function test_service_order_status_transition_is_explicit(): void
    {
        $serviceOrder = ServiceOrder::factory()->create([
            'status' => ServiceOrderStatus::Pending,
        ]);

        $serviceOrder->changeStatus(ServiceOrderStatus::InProgress);
        $serviceOrder->refresh();

        $this->assertSame(ServiceOrderStatus::InProgress, $serviceOrder->status);

        $serviceOrder->changeStatus(ServiceOrderStatus::Completed);
        $serviceOrder->refresh();

        $this->assertSame(ServiceOrderStatus::Completed, $serviceOrder->status);

        $this->expectException(InvalidServiceOrderStatusTransition::class);

        $serviceOrder->changeStatus(ServiceOrderStatus::Pending);
    }
}
