<?php

namespace Database\Factories;

use App\Models\OdsChecklist;
use App\Models\ServiceOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OdsChecklist>
 */
class OdsChecklistFactory extends Factory
{
    protected $model = OdsChecklist::class;

    public function definition(): array
    {
        return [
            'service_order_id' => ServiceOrder::factory(),
            'label' => fake()->sentence(4),
            'is_completed' => fake()->boolean(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
