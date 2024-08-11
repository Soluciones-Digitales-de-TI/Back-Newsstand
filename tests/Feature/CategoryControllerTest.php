<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Crea un usuario y un token
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /**
     * Test the index method.
     */
    public function testIndex()
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);
            /*->assertJsonStructure([
                'success' => true,
                'data' => [
                    'models' => [
                        '*' => '*'
                    ],
                    'total' => '*'
                ]
            ]);*/
    }

    /**
     * Test the store method.
     */
    public function testStore()
    {
        $data = [
            'name' => $this->faker->word,
            'icon' => $this->faker->word,
            'state' => 1
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }

    /**
     * Test the show method.
     */
    public function testShow()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon,
                'state' => $category->state,
            ]);
    }

    /**
     * Test the update method.
     */
    public function testUpdate()
    {
        $category = Category::factory()->create();
        $data = [
            'name' => $this->faker->unique->word,
            'icon' => $this->faker->imageUrl(64,64),
            'state' => 1
        ];

        $response = $this->putJson("/api/v1/categories/{$category->id}", $data);

        $response->assertStatus(200);
            // ->assertJsonFragment($data);
    }

    /**
     * Test the destroy method.
     */
    public function testDestroy()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(204);
    }
}
