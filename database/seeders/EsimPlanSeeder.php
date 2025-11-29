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

        // Define all 15 eSIM plans
        $plans = [
            // Basic Plans
            [
                'category_id' => 1,
                'plan_name' => 'Basic Starter',
                'data' => '1GB',
                'description' => 'Ideal entry-level plan for organizations needing minimal connectivity. Suitable for secure messaging, basic email communication, and occasional web access. Reliable and cost-effective for small-scale operations without compromising data integrity.',
                'validity_days' => 30,
                'price' => 5.00,
            ],
            [
                'category_id' => 1,
                'plan_name' => 'Basic Plus',
                'data' => '3GB',
                'description' => 'Designed for slightly heavier usage, this plan supports light application usage and web browsing. Perfect for administrative departments or field staff requiring consistent and secure mobile data access without large expenses.',
                'validity_days' => 30,
                'price' => 8.00,
            ],
            [
                'category_id' => 1,
                'plan_name' => 'Basic Extended',
                'data' => '5GB',
                'description' => 'Extended plan for regular connectivity needs. Supports secure cloud access, reporting, and lightweight collaboration tools for teams. Optimized for cost-conscious government departments or small offices requiring reliable mobile connectivity.',
                'validity_days' => 60,
                'price' => 12.00,
            ],
            [
                'category_id' => 1,
                'plan_name' => 'Basic Flex',
                'data' => '7GB',
                'description' => 'Flexible data plan for occasional heavy users. Ideal for field teams or consultants who need to transmit documents, access government portals, and stay connected securely while traveling between sites. Balance of affordability and functionality.',
                'validity_days' => 60,
                'price' => 15.00,
            ],
            [
                'category_id' => 1,
                'plan_name' => 'Basic Max',
                'data' => '10GB',
                'description' => 'Maximum data allowance for basic users. Supports moderate video conferencing, secure file transfers, and uninterrupted communication. Designed for departments requiring consistent connectivity with minimal downtime at an affordable rate.',
                'validity_days' => 90,
                'price' => 20.00,
            ],

            // Pro Plans
            [
                'category_id' => 2,
                'plan_name' => 'Pro Starter',
                'data' => '10GB',
                'description' => 'Professional-grade plan for teams and businesses that require reliable and secure data connectivity. Supports real-time communication apps, cloud document management, and collaborative tools essential for smooth operations.',
                'validity_days' => 30,
                'price' => 25.00,
            ],
            [
                'category_id' => 2,
                'plan_name' => 'Pro Plus',
                'data' => '15GB',
                'description' => 'Enhanced plan with additional data for teams with higher communication and collaboration needs. Suitable for departments handling critical workflows, secure video meetings, and cloud-based applications.',
                'validity_days' => 30,
                'price' => 35.00,
            ],
            [
                'category_id' => 2,
                'plan_name' => 'Pro Extended',
                'data' => '20GB',
                'description' => 'Extended connectivity plan for professional users requiring consistent access to cloud services, secure portals, and remote collaboration tools. Supports mobile workforce and remote offices with guaranteed performance and security.',
                'validity_days' => 60,
                'price' => 50.00,
            ],
            [
                'category_id' => 2,
                'plan_name' => 'Pro Flex',
                'data' => '25GB',
                'description' => 'Flexible high-data plan designed for teams managing large datasets, streaming important briefings, and accessing sensitive applications remotely. Offers scalability and secure access for dynamic operations.',
                'validity_days' => 60,
                'price' => 60.00,
            ],
            [
                'category_id' => 2,
                'plan_name' => 'Pro Max',
                'data' => '30GB',
                'description' => 'Maximum professional plan offering high bandwidth for large teams and intensive operations. Supports uninterrupted communication, secure cloud computing, and remote access to critical systems. Ideal for large departments or projects with significant data requirements.',
                'validity_days' => 90,
                'price' => 80.00,
            ],

            // VIP Plans
            [
                'category_id' => 3,
                'plan_name' => 'VIP Starter',
                'data' => '50GB',
                'description' => 'Premium plan for VIP clients and high-priority users. Ensures high-speed, secure connectivity for critical operations, data-intensive applications, and priority network access. Perfect for senior officials or project leads.',
                'validity_days' => 30,
                'price' => 100.00,
            ],
            [
                'category_id' => 3,
                'plan_name' => 'VIP Plus',
                'data' => '75GB',
                'description' => 'Advanced VIP plan offering extended bandwidth for multiple devices and high-demand operations. Suitable for mission-critical projects, remote offices, and teams requiring fast, reliable, and secure connectivity at all times.',
                'validity_days' => 60,
                'price' => 150.00,
            ],
            [
                'category_id' => 3,
                'plan_name' => 'VIP Extended',
                'data' => '100GB',
                'description' => 'Extended high-capacity plan for VIP clients needing sustained connectivity. Supports extensive collaboration, secure document transfers, and uninterrupted video conferencing across multiple devices and remote locations.',
                'validity_days' => 60,
                'price' => 200.00,
            ],
            [
                'category_id' => 3,
                'plan_name' => 'VIP Flex',
                'data' => '150GB',
                'description' => 'Flexible VIP plan offering massive data allowances for critical operations and high-priority projects. Supports multiple team members, advanced analytics, and remote system access without compromise on speed or security.',
                'validity_days' => 90,
                'price' => 300.00,
            ],
            [
                'category_id' => 3,
                'plan_name' => 'VIP Max',
                'data' => '200GB',
                'description' => 'Ultimate VIP plan providing maximum bandwidth and priority connectivity for essential government and enterprise users. Ideal for operations that require uninterrupted data, advanced communication tools, and the highest security standards.',
                'validity_days' => 120,
                'price' => 500.00,
            ],
        ];

        // Create plans and their eSIMs
        foreach ($plans as $planData) {
            $plan = EsimPlan::create($planData);

            // Generate eSIMs
            $esimCount = rand(3, 5);
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
