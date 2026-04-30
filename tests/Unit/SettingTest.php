<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_setting_with_default(): void
    {
        $value = Setting::get('non_existent_key', 'default_value');

        $this->assertSame('default_value', $value);
    }

    public function test_can_set_and_get_setting(): void
    {
        Setting::set('site_title', 'My Blog');

        $this->assertSame('My Blog', Setting::get('site_title'));
        $this->assertDatabaseHas('settings', [
            'key' => 'site_title',
            'value' => 'My Blog',
        ]);
    }

    public function test_setting_update_overrides_existing_value(): void
    {
        Setting::set('site_title', 'Old Title');
        Setting::set('site_title', 'New Title');

        $this->assertSame('New Title', Setting::get('site_title'));
        $this->assertDatabaseMissing('settings', [
            'key' => 'site_title',
            'value' => 'Old Title',
        ]);
    }

    public function test_setting_uses_cache(): void
    {
        Setting::set('cached_key', 'cached_value');
        Setting::get('cached_key'); // populate cache

        $this->assertTrue(Cache::tags(['settings'])->has('cached_key'));
        $this->assertSame('cached_value', Cache::tags(['settings'])->get('cached_key'));
    }

    public function test_setting_set_flushes_cache(): void
    {
        Setting::set('key1', 'value1');
        Setting::get('key1'); // populate cache
        $this->assertTrue(Cache::tags(['settings'])->has('key1'));

        Setting::set('key2', 'value2');
        $this->assertFalse(Cache::tags(['settings'])->has('key1'));
    }
}
