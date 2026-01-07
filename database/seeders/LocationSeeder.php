<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::create([
            'name' => 'Main Building',
            'code' => 'MB',
            'description' => 'Main storage and distribution building',
            'status' => 'active',
        ]);

        Location::create([
            'name' => 'Account Building',
            'code' => 'AB',
            'description' => 'Accounts and administrative offices',
            'status' => 'active',
        ]);

        Location::create([
            'name' => 'Packaging Building',
            'code' => 'PB',
            'description' => 'Packaging and shipping operations',
            'status' => 'active',
        ]);
    }
}
