<?php

namespace Database\Factories;

use App\Domain\ServiceOrders\ServiceOrderStatus;
use App\Models\Category;
use App\Models\Secretariat;
use App\Models\ServiceOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceOrder>
 */
class ServiceOrderFactory extends Factory
{
    protected $model = ServiceOrder::class;

    public function definition(): array
    {
        $secretariat = Secretariat::factory();

        return [
            'title' => fake()->sentence(3),
            'location' => fake()->address(),
            'observation' => fake()->sentence(),
            'due_date' => fake()->date(),
            'is_urgent' => fake()->boolean(),
            'status' => fake()->randomElement(ServiceOrderStatus::cases()),
            'secretariat_id' => $secretariat,
            'category_id' => Category::factory()->state([
                'secretariat_id' => $secretariat,
            ]),
        ];
    }

    public function forSecretariat(Secretariat $secretariat): static
    {
        return $this->state(fn () => [
            'secretariat_id' => $secretariat->id,
            'category_id' => Category::factory()->state([
                'secretariat_id' => $secretariat->id,
            ]),
        ]);
    }

    public function forCategory(Category $category): static
    {
        return $this->state(fn () => [
            'secretariat_id' => $category->secretariat_id,
            'category_id' => $category->id,
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => [
            'is_urgent' => true,
        ]);
    }

    public function withStatus(ServiceOrderStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
        ]);
    }
}
