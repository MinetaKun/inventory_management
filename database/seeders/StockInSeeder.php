<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Seeder;

class StockInSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first user (seeded in DatabaseSeeder)
        $user = User::first();

        if (!$user) {
            $this->command->warn('No user found. Please run DatabaseSeeder first.');
            return;
        }

        // Get all locations
        $mainBuilding = Location::where('code', 'MB')->first();
        $accountBuilding = Location::where('code', 'AB')->first();
        $packagingBuilding = Location::where('code', 'PB')->first();

        if (!$mainBuilding || !$accountBuilding || !$packagingBuilding) {
            $this->command->warn('Locations not found. Please run LocationSeeder first.');
            return;
        }

        // Get all variants
        $variants = Variant::all();

        if ($variants->isEmpty()) {
            $this->command->warn('No variants found. Please run VariantSeeder first.');
            return;
        }

        $this->command->info('Creating stock in records...');

        // Create stock in records for each variant
        $stockInData = [
            // Bed Bunny variants - Initial stock at Main Building
            [
                'sku' => 'CS-HOL-BEDBNY-1-12CM-#1',
                'location' => $mainBuilding,
                'quantity' => 100,
                'notes' => 'Initial shipment from supplier - Q1 2026',
                'days_ago' => 15,
            ],
            [
                'sku' => 'CS-HOL-BEDBNY-1-12CM-#2',
                'location' => $mainBuilding,
                'quantity' => 150,
                'notes' => 'Initial shipment from supplier - Q1 2026',
                'days_ago' => 15,
            ],
            [
                'sku' => 'CS-HOL-BEDBNY-1-12CM-#4',
                'location' => $packagingBuilding,
                'quantity' => 80,
                'notes' => 'Restocking for packaging department',
                'days_ago' => 10,
            ],
            // Premium Dog Bed variants
            [
                'sku' => 'CS-DOGB-BED-PREM-1-2CM-#1',
                'location' => $mainBuilding,
                'quantity' => 50,
                'notes' => 'New product line - initial inventory',
                'days_ago' => 12,
            ],
            [
                'sku' => 'CS-DOGB-BED-PREM-1-4CM-#1',
                'location' => $mainBuilding,
                'quantity' => 40,
                'notes' => 'New product line - initial inventory',
                'days_ago' => 12,
            ],
            [
                'sku' => 'CS-DOGB-BED-PREM-1-12CM-#1',
                'location' => $accountBuilding,
                'quantity' => 25,
                'notes' => 'Sample stock for display',
                'days_ago' => 8,
            ],
            // Felt Heart variants
            [
                'sku' => 'CS-PET-FELTHRT-1-#1',
                'location' => $mainBuilding,
                'quantity' => 200,
                'notes' => 'Bulk order - Valentine season preparation',
                'days_ago' => 7,
            ],
            [
                'sku' => 'CS-PET-FELTHRT-1-#2',
                'location' => $mainBuilding,
                'quantity' => 175,
                'notes' => 'Bulk order - Valentine season preparation',
                'days_ago' => 7,
            ],
            [
                'sku' => 'CS-PET-FELTHRT-1-#4',
                'location' => $packagingBuilding,
                'quantity' => 120,
                'notes' => 'Ready for packaging and shipment',
                'days_ago' => 5,
            ],
            // Additional stock ins (recent)
            [
                'sku' => 'CS-HOL-BEDBNY-1-12CM-#1',
                'location' => $packagingBuilding,
                'quantity' => 30,
                'notes' => 'Emergency restock for pending orders',
                'days_ago' => 3,
            ],
            [
                'sku' => 'CS-PET-FELTHRT-1-#1',
                'location' => $packagingBuilding,
                'quantity' => 50,
                'notes' => 'High demand - additional stock transfer',
                'days_ago' => 2,
            ],
            [
                'sku' => 'CS-DOGB-BED-PREM-1-4CM-#1',
                'location' => $accountBuilding,
                'quantity' => 10,
                'notes' => 'Display units for showroom',
                'days_ago' => 1,
            ],
        ];

        foreach ($stockInData as $data) {
            $variant = $variants->where('sku', $data['sku'])->first();

            if ($variant) {
                // Create stock movement record
                StockMovement::create([
                    'variant_id' => $variant->id,
                    'location_id' => $data['location']->id,
                    'shelf_id' => null,
                    'moved_by' => $user->id,
                    'type' => 'in',
                    'quantity' => $data['quantity'],
                    'reason' => 'Initial Stock In',
                    'notes' => $data['notes'],
                    'status' => 'completed',
                    'created_at' => now()->subDays($data['days_ago']),
                    'updated_at' => now()->subDays($data['days_ago']),
                ]);

                // Update variant quantity
                $variant->increment('quantity', $data['quantity']);

                $this->command->line("  ✓ Added {$data['quantity']} units of {$data['sku']} to {$data['location']->name}");
            } else {
                $this->command->warn("  ✗ Variant {$data['sku']} not found");
            }
        }

        $this->command->info('Stock in seeding completed!');
        $this->command->info('Total stock movements created: ' . count($stockInData));
    }
}
