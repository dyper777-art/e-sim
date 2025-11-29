<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EsimPlan;
use App\Models\Esim;
use App\Models\Category;

class EsimPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure categories exist
        $categories = ['Basic', 'Pro', 'VIP'];
        foreach ($categories as $index => $name) {
            Category::firstOrCreate(
                ['id' => $index + 1],
                ['name' => $name]
            );
        }

        // Mapping images based on plan type and category
        $imageMap = [
            'Starter'  => ['Starter.png', 'Starter-1.png', 'Starter-2.png'],
            'Plus'     => ['Plus.png', 'Plus-1.png', 'Plus-2.png'],
            'Extended' => ['Extended.png', 'Extended-1.png', 'Extension.png'],
            'Flex'     => ['Flex.png', 'Flex-1.png', 'Flex-2.png'],
            'Max'      => ['Max.png', 'Max-1.png', 'Max-2.png'],
        ];

        // Define all 15 eSIM plans
        $plans = [
            // Basic Plans
            ['category_id' => 1, 'plan_name' => 'Basic Starter', 'data' => '1GB', 'description' => 'Ideal entry-level plan...', 'validity_days' => 30, 'price' => 5.00],
            ['category_id' => 1, 'plan_name' => 'Basic Plus', 'data' => '3GB', 'description' => 'Designed for slightly heavier usage...', 'validity_days' => 30, 'price' => 8.00],
            ['category_id' => 1, 'plan_name' => 'Basic Extended', 'data' => '5GB', 'description' => 'Extended plan for regular connectivity...', 'validity_days' => 60, 'price' => 12.00],
            ['category_id' => 1, 'plan_name' => 'Basic Flex', 'data' => '7GB', 'description' => 'Flexible data plan for occasional heavy users...', 'validity_days' => 60, 'price' => 15.00],
            ['category_id' => 1, 'plan_name' => 'Basic Max', 'data' => '10GB', 'description' => 'Maximum data allowance for basic users...', 'validity_days' => 90, 'price' => 20.00],

            // Pro Plans
            ['category_id' => 2, 'plan_name' => 'Pro Starter', 'data' => '10GB', 'description' => 'Professional-grade plan...', 'validity_days' => 30, 'price' => 25.00],
            ['category_id' => 2, 'plan_name' => 'Pro Plus', 'data' => '15GB', 'description' => 'Enhanced plan with additional data...', 'validity_days' => 30, 'price' => 35.00],
            ['category_id' => 2, 'plan_name' => 'Pro Extended', 'data' => '20GB', 'description' => 'Extended connectivity plan...', 'validity_days' => 60, 'price' => 50.00],
            ['category_id' => 2, 'plan_name' => 'Pro Flex', 'data' => '25GB', 'description' => 'Flexible high-data plan...', 'validity_days' => 60, 'price' => 60.00],
            ['category_id' => 2, 'plan_name' => 'Pro Max', 'data' => '30GB', 'description' => 'Maximum professional plan...', 'validity_days' => 90, 'price' => 80.00],

            // VIP Plans
            ['category_id' => 3, 'plan_name' => 'VIP Starter', 'data' => '50GB', 'description' => 'Premium plan for VIP clients...', 'validity_days' => 30, 'price' => 100.00],
            ['category_id' => 3, 'plan_name' => 'VIP Plus', 'data' => '75GB', 'description' => 'Advanced VIP plan...', 'validity_days' => 60, 'price' => 150.00],
            ['category_id' => 3, 'plan_name' => 'VIP Extended', 'data' => '100GB', 'description' => 'Extended high-capacity plan...', 'validity_days' => 60, 'price' => 200.00],
            ['category_id' => 3, 'plan_name' => 'VIP Flex', 'data' => '150GB', 'description' => 'Flexible VIP plan...', 'validity_days' => 90, 'price' => 300.00],
            ['category_id' => 3, 'plan_name' => 'VIP Max', 'data' => '200GB', 'description' => 'Ultimate VIP plan...', 'validity_days' => 120, 'price' => 500.00],
        ];

        foreach ($plans as $planData) {
            // Extract type from plan_name (Starter, Plus, etc.)
            preg_match('/(Starter|Plus|Extended|Flex|Max)/', $planData['plan_name'], $matches);
            $type = $matches[0] ?? 'Starter';

            // Assign correct image based on category and type
            $categoryIndex = $planData['category_id'] - 1; // 0-based index
            $planData['image'] = 'frontend/assets/images/' . $imageMap[$type][$categoryIndex];

            // Create plan
            $plan = EsimPlan::create($planData);

            // Generate eSIMs
            $esimCount = rand(150, 200);
            Esim::factory()->count($esimCount)->create([
                'esim_plan_id' => $plan->id,
            ]);

            // Update quantity
            $plan->update([
                'quantity' => $plan->esims()->count(),
            ]);
        }
    }
}
