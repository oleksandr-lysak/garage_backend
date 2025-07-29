<?php

namespace Tests\Unit;

use App\Http\Services\PaginatorService;
use Tests\TestCase;

class PaginatorServiceTest extends TestCase
{
    public function test_paginate_returns_length_aware_paginator()
    {
        $service = new PaginatorService;
        $items = [1, 2, 3];
        $paginator = $service->paginate($items, 3, 2, 1);

        $this->assertEquals(2, $paginator->perPage());
        $this->assertEquals(3, $paginator->total());
        $this->assertEquals([1, 2, 3], $paginator->items());
    }
}
