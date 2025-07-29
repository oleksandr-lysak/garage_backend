<?php

namespace Tests\Unit;

use App\Rules\Base64Image;
use Tests\TestCase;

class Base64ImageRuleTest extends TestCase
{
    public function test_passes_on_valid_base64_image()
    {
        // 1x1 px transparent GIF
        $valid = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
        $rule = new Base64Image;
        $this->assertTrue($rule->passes('photo', $valid));
    }

    public function test_fails_on_invalid_string()
    {
        $invalid = 'not-a-base64-image';
        $rule = new Base64Image;
        $this->assertFalse($rule->passes('photo', $invalid));
    }

    public function test_message_returns_string()
    {
        $rule = new \App\Rules\Base64Image;
        $this->assertIsString($rule->message());
    }
}
