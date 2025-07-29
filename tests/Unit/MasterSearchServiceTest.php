<?php

namespace Tests\Unit;

use App\Http\Services\Master\MasterSearchService;
use ReflectionClass;
use Tests\TestCase;

class MasterSearchServiceTest extends TestCase
{
    public function test_calculate_search_radius()
    {
        $zoom = 4;
        $expected = 20037.5 / $zoom;

        $ref = new ReflectionClass(MasterSearchService::class);
        $method = $ref->getMethod('calculateSearchRadius');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke(null, $zoom));
    }
}
