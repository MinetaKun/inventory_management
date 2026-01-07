<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        Attribute::create([
            'name' => 'Size',
            'status' => 'active',
        ]);
        Attribute::create([
            'name' => 'Base Color',
            'status' => 'active',
        ]);
    }
}
