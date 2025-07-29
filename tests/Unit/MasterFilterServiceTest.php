<?php

namespace Tests\Unit;

use App\Http\Services\Master\MasterFilterService;
use Tests\TestCase;

class MasterFilterServiceTest extends TestCase
{
    public function test_apply_filters_appends_where_clauses()
    {
        $filters = [
            'name' => 'Ivan',
            'service_id' => 3,
            'rating' => 4,
        ];

        $query = 'SELECT * FROM masters WHERE latitude > 0';
        $params = [];

        MasterFilterService::applyFilters($filters, $query, $params);

        $this->assertStringContainsString('masters.name LIKE :name', $query);
        $this->assertStringContainsString('masters.service_id = :service_id', $query);
        $this->assertStringContainsString('COALESCE(reviews_summary.rating, 0) >= :rating', $query);

        $this->assertEquals('%Ivan%', $params['name']);
        $this->assertEquals(3, $params['service_id']);
        $this->assertEquals(4, $params['rating']);
    }

    public function test_apply_filters_with_empty_filters_does_nothing()
    {
        $query = 'SELECT * FROM masters';
        $params = [];

        MasterFilterService::applyFilters([], $query, $params);

        $this->assertEquals('SELECT * FROM masters', $query);
        $this->assertEmpty($params);
    }
}
