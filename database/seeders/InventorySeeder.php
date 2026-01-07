<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Category;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $petCategory = Category::where('code', 'PET')->first();
        $dogBedCategory = Category::where('code', 'DOGB')->first();
        $holidayCategory = Category::where('code', 'HOL')->first();

        $products = [
            [
                'product_name' => '12cm Hiding Bed Bunny',
                'product_label' => 'BEDBNY',
                'sku_ref' => 'CS-HOL-BEDBNY-1',
                'category_id' => $holidayCategory?->id,
                'weight' => 0.5,
                'cost_price' => 15.99,
                'status' => 'active',
            ],
            [
                'product_name' => 'Felt Heart Shape',
                'product_label' => 'FELTHRT',
                'sku_ref' => 'CS-PET-FELTHRT-1',
                'category_id' => $petCategory?->id,
                'weight' => 0.25,
                'cost_price' => 8.50,
                'status' => 'active',
            ],
            [
                'product_name' => 'Premium Dog Bed',
                'product_label' => 'DOGBED-PREMIUM',
                'sku_ref' => 'CS-DOGB-BED-PREM-1',
                'category_id' => $dogBedCategory?->id,
                'weight' => 2.5,
                'cost_price' => 45.00,
                'status' => 'active',
            ],
            [
                'product_name' => 'Pet Toy Ball',
                'product_label' => 'PETBALL',
                'sku_ref' => 'CS-PET-BALL-001',
                'category_id' => $petCategory?->id,
                'weight' => 0.15,
                'cost_price' => 3.99,
                'status' => 'active',
            ],
            [
                'product_name' => 'Holiday Ornament Set',
                'product_label' => 'ORNAMENT-SET',
                'sku_ref' => 'CS-HOL-ORNY-SET-5',
                'category_id' => $holidayCategory?->id,
                'weight' => 1.0,
                'cost_price' => 24.99,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Inventory::create([
                ...$product,
                'added_by' => 1,
            ]);
        }
    }
}
