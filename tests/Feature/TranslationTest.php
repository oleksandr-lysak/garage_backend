<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\App;

class TranslationTest extends TestCase
{
    public function test_barbershop_translation_uk(): void
    {
        App::setLocale('uk');
        $this->assertEquals('Барбершоп', __('data.services.barbershop'));
    }
}
