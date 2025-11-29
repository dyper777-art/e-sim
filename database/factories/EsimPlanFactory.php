<?php

namespace Database\Factories;

use App\Models\EsimPlan;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class EsimPlanFactory extends Factory
{
    protected $model = EsimPlan::class;

    public function definition(): array
    {
        // Pick a category or create one
        $category = Category::inRandomOrder()->first() ?: Category::factory()->create();

        // Define plan name options per category
        $themes = [
            'VIP'   => ['Elite', 'Premium', 'Luxury'],
            'Basic' => ['Starter', 'Standard', 'Essential'],
            'Pro'   => ['Advanced', 'Pro', 'Unlimited'],
        ];

        $planWords = $themes[$category->name] ?? ['Standard'];

        // Ensure uniqueness using Faker's unique() method
        $planName = $this->faker->unique()->randomElement($planWords) . ' Plan';

        return [
            'category_id'   => $category->id,
            'plan_name'     => $planName,
            'description'   => $this->faker->sentence(10),
            'data'          => $this->faker->numberBetween(1, 100),
            'validity_days' => $this->faker->numberBetween(7, 365),
            'price'         => $this->faker->randomFloat(2, 5, 100),
            'quantity'      => 0,
        ];
    }
}
