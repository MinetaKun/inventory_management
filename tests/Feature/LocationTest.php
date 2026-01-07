<?php

namespace Tests\Feature;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_locations(): void
    {
        Location::create([
            'name' => 'Main Building',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/locations');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_can_create_location(): void
    {
        $response = $this->postJson('/api/locations', [
            'name' => 'Warehouse',
            'status' => 'active',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.name', 'Warehouse');
        $this->assertDatabaseHas('locations', [
            'name' => 'Warehouse',
        ]);
    }

    public function test_can_update_location(): void
    {
        $location = Location::create([
            'name' => 'Main Building',
            'status' => 'active',
        ]);

        $response = $this->putJson("/api/locations/{$location->id}", [
            'name' => 'Updated Main Building',
            'status' => 'inactive',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Updated Main Building',
            'status' => 'inactive',
        ]);
    }

    public function test_can_soft_delete_location(): void
    {
        $location = Location::create([
            'name' => 'Main Building',
            'status' => 'active',
        ]);

        $response = $this->deleteJson("/api/locations/{$location->id}");

        $response->assertOk();
        $this->assertSoftDeleted('locations', [
            'id' => $location->id,
        ]);
    }
}
