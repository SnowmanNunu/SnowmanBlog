<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_media_url_returns_null_for_null_path(): void
    {
        $this->assertNull(media_url(null));
    }

    public function test_media_url_returns_null_for_empty_string(): void
    {
        $this->assertNull(media_url(''));
    }

    public function test_media_url_returns_storage_url_for_public_disk(): void
    {
        Storage::fake('public');
        config()->set('filesystems.media_disk', 'public');

        $url = media_url('images/test.jpg');

        $this->assertStringContainsString('images/test.jpg', $url);
    }

    public function test_media_url_uses_configured_media_disk(): void
    {
        Storage::fake('custom');
        config()->set('filesystems.media_disk', 'custom');

        $url = media_url('file.pdf');

        $this->assertStringContainsString('file.pdf', $url);
    }
}
