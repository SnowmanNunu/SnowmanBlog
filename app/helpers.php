<?php

if (! function_exists('media_url')) {
    /**
     * Generate a URL for a media file using the configured media disk.
     *
     * @param  string|null  $path
     * @return string|null
     */
    function media_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk(config('filesystems.media_disk', 'public'))->url($path);
    }
}
