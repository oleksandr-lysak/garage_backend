<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class PhotoHelper
{
    public function downloadAndConvertToBase64(string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return null;
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        $base64 = base64_encode($imageData);

        return "data:$mimeType;base64,$base64";
    }

    /**
     * Persist a base64-encoded image to the public storage disk and return the relative file path.
     */
    public function saveBase64(string $base64): ?string
    {
        if (empty($base64)) {
            return null;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $extension = strtolower($matches[1]);
            $base64 = substr($base64, strpos($base64, ',') + 1);
        } else {
            return null;
        }

        /** @var string|false $decoded */
        $decoded = base64_decode($base64);
        if ($decoded === false) {
            return null;
        }

        $fileName = 'images/' . uniqid('', true) . '.' . $extension;
        Storage::disk('public')->put($fileName, $decoded);

        return $fileName;
    }
}
