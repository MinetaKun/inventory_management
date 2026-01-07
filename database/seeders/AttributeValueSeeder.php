<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        $size = Attribute::where('name', 'Size')->first();
        $color = Attribute::where('name', 'Base Color')->first();

        foreach (['2cm', '4cm', '12cm'] as $val) {
            AttributeValue::create([
                'attribute_id' => $size?->id,
                'value' => $val,
                'status' => 'active',
            ]);
        }
        foreach (['#1', '#2', '#4'] as $val) {
            AttributeValue::create([
                'attribute_id' => $color?->id,
                'value' => $val,
                'status' => 'active',
            ]);
        }
    }
}
