<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Helpers\PhotoHelper;
use Tests\TestCase;

class PhotoHelperTest extends TestCase
{
    public function test_download_and_convert_returns_null_on_empty_url(): void
    {
        $helper = new PhotoHelper;
        $this->assertNull($helper->downloadAndConvertToBase64(''));
    }

    public function test_download_and_convert_returns_null_on_invalid_url(): void
    {
        $helper = new PhotoHelper;
        // invalid url expected to fail
        $this->assertNull($helper->downloadAndConvertToBase64('http://nonexistent.localhost/image.jpg'));
    }

    public function test_download_and_convert_success(): void
    {
        // create temp gif
        $binary = base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
        $tempPath = storage_path('app/tmp_test.gif');
        file_put_contents($tempPath, $binary);
        $url = 'file://'.$tempPath;

        $helper = new \App\Helpers\PhotoHelper;
        $result = $helper->downloadAndConvertToBase64($url);

        $this->assertNotNull($result);
        $this->assertStringStartsWith('data:image/', $result);

        unlink($tempPath);
    }
}
