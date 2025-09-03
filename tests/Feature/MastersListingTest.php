<?php

namespace Tests\Feature;

use App\Models\Master;
use App\Models\Specialization;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MastersListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_masters_listing_page_loads()
    {
        $response = $this->get('/masters');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('MastersList'));
    }

    public function test_masters_api_returns_paginated_results()
    {
        // Create test masters
        Master::factory()->count(25)->create();

        $response = $this->getJson('/api/masters?page=1&per_page=10');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'masters' => [
                'data',
                'current_page',
                'last_page',
                'total',
                'prev_page_url',
                'next_page_url',
            ]
        ]);

        $this->assertEquals(10, count($response->json('masters.data')));
        $this->assertEquals(25, $response->json('masters.total'));
    }

    public function test_masters_api_supports_search()
    {
        Master::factory()->create(['name' => 'John Doe']);
        Master::factory()->create(['name' => 'Jane Smith']);

        $response = $this->getJson('/api/masters?search=John');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('masters.data')));
        $this->assertEquals('John Doe', $response->json('masters.data.0.name'));
    }

    public function test_masters_api_supports_filtering()
    {
        $specialization = Specialization::factory()->create(['name' => 'Engine Repair']);
        $master = Master::factory()->create(['rating' => 4.8]);
        $master->specializations()->attach($specialization);

        $response = $this->getJson('/api/masters?specialization=' . $specialization->id . '&min_rating=4.5');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('masters.data')));
    }

    public function test_masters_api_supports_sorting()
    {
        Master::factory()->create(['rating' => 3.0]);
        Master::factory()->create(['rating' => 5.0]);
        Master::factory()->create(['rating' => 4.0]);

        $response = $this->getJson('/api/masters?sort_by=rating');

        $response->assertStatus(200);
        $data = $response->json('masters.data');
        $this->assertEquals(5.0, $data[0]['rating']);
        $this->assertEquals(4.0, $data[1]['rating']);
        $this->assertEquals(3.0, $data[2]['rating']);
    }

    public function test_filters_api_returns_data()
    {
        Specialization::factory()->count(3)->create();
        Service::factory()->count(5)->create();
        Master::factory()->count(2)->create(['city' => 'Київ']);

        $response = $this->getJson('/api/masters/filters');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'specializations',
            'services',
            'cities'
        ]);

        $this->assertEquals(3, count($response->json('specializations')));
        $this->assertEquals(5, count($response->json('services')));
        $this->assertContains('Київ', $response->json('cities'));
    }

    public function test_master_detail_page_loads()
    {
        $master = Master::factory()->create(['slug' => 'test-master']);

        $response = $this->get('/masters/' . $master->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Master'));
    }

    public function test_master_detail_page_returns_404_for_invalid_slug()
    {
        $response = $this->get('/masters/invalid-slug');

        $response->assertStatus(404);
    }

    public function test_masters_listing_page_has_seo_meta_tags()
    {
        $response = $this->get('/masters');

        $response->assertStatus(200);

        // Check if the page contains basic SEO elements
        $response->assertInertia(fn ($page) =>
            $page->has('seoConfig') &&
            $page->get('seoConfig')['type'] === 'auto_mechanics'
        );
    }

    public function test_masters_api_handles_empty_results()
    {
        $response = $this->getJson('/api/masters?search=nonexistent');

        $response->assertStatus(200);
        $this->assertEquals(0, count($response->json('masters.data')));
        $this->assertEquals(0, $response->json('masters.total'));
    }

    public function test_masters_api_handles_invalid_parameters()
    {
        $response = $this->getJson('/api/masters?page=invalid&per_page=invalid');

        $response->assertStatus(200); // Should handle gracefully
    }

    public function test_masters_api_supports_pagination()
    {
        Master::factory()->count(35)->create();

        $response = $this->getJson('/api/masters?page=2&per_page=20');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('masters.current_page'));
        $this->assertEquals(20, $response->json('masters.per_page'));
        $this->assertNotNull($response->json('masters.prev_page_url'));
        $this->assertNotNull($response->json('masters.next_page_url'));
    }
}
