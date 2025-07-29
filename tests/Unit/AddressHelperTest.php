<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Helpers\AddressHelper;
use Tests\TestCase;

class AddressHelperTest extends TestCase
{
    public function test_distance_kilometers_between_same_point_returns_zero(): void
    {
        $helper = new AddressHelper;
        $d = $helper->distance(50.0, 30.0, 50.0, 30.0, 'K');
        $this->assertEquals(0.0, $d);
    }

    public function test_distance_kilometers_between_near_points(): void
    {
        $helper = new AddressHelper;
        // lat1, lon1 = 0,0 ; lat2, lon2 = 0,1
        $dKm = $helper->distance(0.0, 0.0, 0.0, 1.0, 'K');
        // 1 degree longitude at equator ≈ 111.32 km
        $this->assertGreaterThan(110.0, $dKm);
        $this->assertLessThan(112.0, $dKm);
    }

    public function test_get_place_id_returns_false_without_api_key(): void
    {
        $helper = new \App\Helpers\AddressHelper;
        $this->assertFalse($helper->getPlaceId(0, 0));
    }
}
