<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Call seeders in order of dependencies
        $this->call([
            CategorySeeder::class,
            LocationSeeder::class,
            ShelfSeeder::class,
            InventorySeeder::class,
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            VariantSeeder::class,
            StockInSeeder::class,  // Must run after VariantSeeder and LocationSeeder
        ]);
    }
}
