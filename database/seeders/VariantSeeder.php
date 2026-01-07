<?php

namespace Database\Seeders;

use App\Models\Variant;
use App\Models\Inventory;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        // Get products
        $bedBunny = Inventory::where('product_label', 'BEDBNY')->first();
        $premiumDogBed = Inventory::where('product_label', 'DOGBED-PREMIUM')->first();
        $feltHeart = Inventory::where('product_label', 'FELTHRT')->first();

        // Get attribute values
        $size2cm = AttributeValue::where('value', '2cm')->first();
        $size4cm = AttributeValue::where('value', '4cm')->first();
        $size12cm = AttributeValue::where('value', '12cm')->first();
        $color1 = AttributeValue::where('value', '#1')->first();
        $color2 = AttributeValue::where('value', '#2')->first();
        $color4 = AttributeValue::where('value', '#4')->first();

        $variants = [
            // 12cm Hiding Bed Bunny variants (different colors)
            [
                'inventory_id' => $bedBunny?->id,
                'sku' => 'CS-HOL-BEDBNY-1-12CM-#1',
                'attribute_values' => [$size12cm?->id, $color1?->id],
                'quantity' => 25,
                'status' => 'active',
            ],
            [
                'inventory_id' => $bedBunny?->id,
                'sku' => 'CS-HOL-BEDBNY-1-12CM-#2',
                'attribute_values' => [$size12cm?->id, $color2?->id],
                'quantity' => 30,
                'status' => 'active',
            ],
            [
                'inventory_id' => $bedBunny?->id,
                'sku' => 'CS-HOL-BEDBNY-1-12CM-#4',
                'attribute_values' => [$size12cm?->id, $color4?->id],
                'quantity' => 20,
                'status' => 'active',
            ],
            // Premium Dog Bed variants (different sizes)
            [
                'inventory_id' => $premiumDogBed?->id,
                'sku' => 'CS-DOGB-BED-PREM-1-2CM-#1',
                'attribute_values' => [$size2cm?->id, $color1?->id],
                'quantity' => 15,
                'status' => 'active',
            ],
            [
                'inventory_id' => $premiumDogBed?->id,
                'sku' => 'CS-DOGB-BED-PREM-1-4CM-#1',
                'attribute_values' => [$size4cm?->id, $color1?->id],
                'quantity' => 12,
                'status' => 'active',
            ],
            [
                'inventory_id' => $premiumDogBed?->id,
                'sku' => 'CS-DOGB-BED-PREM-1-12CM-#1',
                'attribute_values' => [$size12cm?->id, $color1?->id],
                'quantity' => 8,
                'status' => 'active',
            ],
            // Felt Heart Shape variants (different colors)
            [
                'inventory_id' => $feltHeart?->id,
                'sku' => 'CS-PET-FELTHRT-1-#1',
                'attribute_values' => [$color1?->id],
                'quantity' => 50,
                'status' => 'active',
            ],
            [
                'inventory_id' => $feltHeart?->id,
                'sku' => 'CS-PET-FELTHRT-1-#2',
                'attribute_values' => [$color2?->id],
                'quantity' => 45,
                'status' => 'active',
            ],
            [
                'inventory_id' => $feltHeart?->id,
                'sku' => 'CS-PET-FELTHRT-1-#4',
                'attribute_values' => [$color4?->id],
                'quantity' => 40,
                'status' => 'active',
            ],
        ];

        foreach ($variants as $variantData) {
            $attributeValues = $variantData['attribute_values'];
            unset($variantData['attribute_values']);

            $variant = Variant::create($variantData);

            // Attach attribute values with their attribute IDs
            $pivotData = [];
            foreach ($attributeValues as $attributeValueId) {
                $attributeValue = AttributeValue::find($attributeValueId);
                if ($attributeValue) {
                    $pivotData[$attributeValueId] = [
                        'attribute_id' => $attributeValue->attribute_id,
                    ];
                }
            }

            $variant->attributeValues()->attach($pivotData);
        }
    }
}
