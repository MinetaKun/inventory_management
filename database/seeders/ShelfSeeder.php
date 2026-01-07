<?php

namespace Database\Seeders;

use App\Models\Shelf;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ShelfSeeder extends Seeder
{
    public function run(): void
    {
        $mainBuilding = Location::where('code', 'MB')->first();
        $accountBuilding = Location::where('code', 'AB')->first();
        $packagingBuilding = Location::where('code', 'PB')->first();

        $petCategory = Category::where('code', 'PET')->first();
        $dogBedCategory = Category::where('code', 'DOGB')->first();
        $holidayCategory = Category::where('code', 'HOL')->first();

        // Main Building Shelves
        if ($mainBuilding) {
            Shelf::create([
                'location_id' => $mainBuilding->id,
                'category_id' => $petCategory?->id,
                'name' => 'Shelf A',
                'code' => 'MB-SA-01',
                'description' => 'Pet products shelf A',
                'status' => 'active',
            ]);

            Shelf::create([
                'location_id' => $mainBuilding->id,
                'category_id' => $dogBedCategory?->id,
                'name' => 'Shelf B',
                'code' => 'MB-SB-01',
                'description' => 'Dog bed products shelf',
                'status' => 'active',
            ]);

            Shelf::create([
                'location_id' => $mainBuilding->id,
                'category_id' => $petCategory?->id,
                'name' => 'Shelf C',
                'code' => 'MB-SC-01',
                'description' => 'Additional pet products',
                'status' => 'active',
            ]);
        }

        // Account Building Shelves
        if ($accountBuilding) {
            Shelf::create([
                'location_id' => $accountBuilding->id,
                'category_id' => $holidayCategory?->id,
                'name' => 'Shelf A',
                'code' => 'AB-SA-01',
                'description' => 'Holiday and seasonal items',
                'status' => 'active',
            ]);

            Shelf::create([
                'location_id' => $accountBuilding->id,
                'category_id' => null,
                'name' => 'Shelf B',
                'code' => 'AB-SB-01',
                'description' => 'General storage',
                'status' => 'active',
            ]);
        }

        // Packaging Building Shelves
        if ($packagingBuilding) {
            Shelf::create([
                'location_id' => $packagingBuilding->id,
                'category_id' => null,
                'name' => 'Shelf A',
                'code' => 'PB-SA-01',
                'description' => 'Packaging materials',
                'status' => 'active',
            ]);
        }
    }
}
