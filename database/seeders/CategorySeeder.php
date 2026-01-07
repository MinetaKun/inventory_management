<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Pet Collection',
            'code' => 'PET',
            'status' => 'active',
        ]);

        Category::create([
            'name' => 'Dog Bed',
            'code' => 'DOGB',
            'status' => 'active',
        ]);

        Category::create([
            'name' => 'Holiday & Seasonal Shapes',
            'code' => 'HOL',
            'status' => 'active',
        ]);
    }
}
