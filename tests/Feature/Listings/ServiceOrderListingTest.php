<?php

namespace Tests\Feature\Listings;

use App\Application\ServiceOrders\ListServiceOrders;
use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Models\Category;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOrderListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_order_listing_is_paginated_and_filtered_by_search(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);

        ServiceOrder::factory()->forSecretariat($secretariat)->count(12)->create([
            'category_id' => $category->id,
            'title' => 'Servico comum',
        ]);

        ServiceOrder::factory()->forSecretariat($secretariat)->count(3)->create([
            'category_id' => $category->id,
            'title' => 'Reparo urgente',
            'is_urgent' => true,
        ]);

        $listing = app(ListServiceOrders::class)->handle($secretariat->id, 'reparo', 5);

        $this->assertSame(3, $listing->summary['total']);
        $this->assertSame(3, $listing->summary['urgent']);
        $this->assertSame(3, $listing->serviceOrders->total());
        $this->assertCount(3, $listing->serviceOrders->items());
    }

    public function test_service_order_listing_summary_counts_completed_records(): void
    {
        $secretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);

        ServiceOrder::factory()->forSecretariat($secretariat)->count(2)->create([
            'category_id' => $category->id,
            'status' => ServiceOrderStatus::Completed,
        ]);

        ServiceOrder::factory()->forSecretariat($secretariat)->count(4)->create([
            'category_id' => $category->id,
            'status' => ServiceOrderStatus::Pending,
        ]);

        $listing = app(ListServiceOrders::class)->handle($secretariat->id, '', 3);

        $this->assertSame(6, $listing->summary['total']);
        $this->assertSame(2, $listing->summary['completed']);
        $this->assertCount(3, $listing->serviceOrders->items());
        $this->assertSame(2, $listing->serviceOrders->lastPage());
    }

    public function test_service_order_listing_respects_secretariat_scope(): void
    {
        $secretariat = Secretariat::factory()->create();
        $otherSecretariat = Secretariat::factory()->create();
        $category = Category::factory()->create(['secretariat_id' => $secretariat->id]);
        $otherCategory = Category::factory()->create(['secretariat_id' => $otherSecretariat->id]);

        ServiceOrder::factory()->forSecretariat($secretariat)->count(2)->create([
            'category_id' => $category->id,
            'title' => 'ODS da secretaria correta',
            'status' => ServiceOrderStatus::Pending,
            'is_urgent' => false,
        ]);

        ServiceOrder::factory()->forSecretariat($otherSecretariat)->count(4)->create([
            'category_id' => $otherCategory->id,
            'title' => 'ODS de outra secretaria',
            'is_urgent' => true,
            'status' => ServiceOrderStatus::Completed,
        ]);

        $listing = app(ListServiceOrders::class)->handle($secretariat->id, '', 10);

        $this->assertSame(2, $listing->summary['total']);
        $this->assertSame(0, $listing->summary['urgent']);
        $this->assertSame(0, $listing->summary['completed']);
        $this->assertSame(2, $listing->serviceOrders->total());
    }
}
