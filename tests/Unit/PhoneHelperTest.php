<?php

namespace Tests\Unit;

use App\Helpers\PhoneHelper;
use Tests\TestCase;

class PhoneHelperTest extends TestCase
{
    public function test_normalize_removes_non_digits_and_adds_plus()
    {
        $helper = new PhoneHelper;
        $this->assertEquals('+380501234567', $helper->normalize('  (050) 123-45-67 '));
    }

    public function test_normalize_handles_empty_string(): void
    {
        $helper = new \App\Helpers\PhoneHelper;
        $this->assertEquals('+', $helper->normalize('   '));
    }

    public function test_normalize_no_change_when_already_in_format(): void
    {
        $helper = new \App\Helpers\PhoneHelper;
        $this->assertEquals('+380501234567', $helper->normalize('+380501234567'));
    }
}
