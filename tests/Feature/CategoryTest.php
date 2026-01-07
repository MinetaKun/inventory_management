<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories(): void
    {
        Category::create([
            'name' => 'Pet Collection',
            'code' => 'PET',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/categories');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_can_create_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Dog Bed',
            'code' => 'DOGB',
            'status' => 'active',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.name', 'Dog Bed');
        $this->assertDatabaseHas('categories', [
            'code' => 'DOGB',
        ]);
    }

    public function test_cannot_create_duplicate_code(): void
    {
        Category::create([
            'name' => 'Pet Collection',
            'code' => 'PET',
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/categories', [
            'name' => 'Another Category',
            'code' => 'PET',
            'status' => 'active',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('code');
    }

    public function test_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Pet Collection',
            'code' => 'PET',
            'status' => 'active',
        ]);

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Pet Collection',
            'code' => 'PET',
            'status' => 'inactive',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Pet Collection',
            'status' => 'inactive',
        ]);
    }

    public function test_can_soft_delete_category(): void
    {
        $category = Category::create([
            'name' => 'Pet Collection',
            'code' => 'PET',
            'status' => 'active',
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertOk();
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }
}
