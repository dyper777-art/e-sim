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
            'VIP'   => ['Starter', 'Plus', 'Extended', 'Flex', 'Max'],
            'Basic' => ['Starter', 'Plus', 'Extended', 'Flex', 'Max'],
            'Pro'   => ['Starter', 'Plus', 'Extended', 'Flex', 'Max'],
        ];

        $planWords = $themes[$category->name] ?? ['Standard'];

        // Ensure uniqueness using Faker's unique() method
        $planName = $this->faker->unique()->randomElement($planWords) . ' ' . $category->name;

        // Assign images based on plan type
        $suffix = '';
        foreach (['Starter', 'Plus', 'Extended', 'Flex', 'Max'] as $type) {
            if (str_contains($planName, $type)) {
                $suffix = $type;
                break;
            }
        }

        // Image options (include variants if they exist)
        $images = [
            'Starter'  => ['Starter.png','Starter-1.png','Starter-2.png'],
            'Plus'     => ['Plus.png','Plus-1.png','Plus-2.png'],
            'Extended' => ['Extended.png','Extended-1.png','Extension.png'],
            'Flex'     => ['Flex.png','Flex-1.png','Flex-2.png'],
            'Max'      => ['Max.png','Max-1.png','Max-2.png'],
        ];

        $image = $images[$suffix][array_rand($images[$suffix])] ?? null;

        return [
            'category_id'   => $category->id,
            'plan_name'     => $planName,
            'description'   => $this->faker->sentence(15),
            'image'         => $image,
            'data'          => $this->faker->numberBetween(1, 200) . 'GB',
            'validity_days' => $this->faker->numberBetween(30, 120),
            'price'         => $this->faker->randomFloat(2, 5, 500),
            'quantity'      => 0,
        ];
    }
}
