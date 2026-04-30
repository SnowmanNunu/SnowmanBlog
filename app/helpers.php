<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

if (! function_exists('media_url')) {
    function media_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk(config('filesystems.media_disk', 'public'))->url($path);
    }
}
