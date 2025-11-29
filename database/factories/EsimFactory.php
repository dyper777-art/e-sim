<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EsimPlan;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Esim>
 */
class EsimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'esim_plan_id' => EsimPlan::factory(),
            'phone_number' => '0' . $this->faker->unique()->numerify('##########'),
            'assigned_to'  => null,
        ];
    }
}
