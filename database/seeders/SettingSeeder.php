<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_title', 'value' => 'SnowmanBlog', 'label' => '博客标题', 'type' => 'text'],
            ['key' => 'site_icp', 'value' => '', 'label' => '备案号', 'type' => 'text'],
            ['key' => 'site_author', 'value' => 'SnowmanNunu', 'label' => '作者名称', 'type' => 'text'],
            ['key' => 'site_description', 'value' => '记录技术成长，分享编程知识', 'label' => '站点描述', 'type' => 'textarea'],
        ];

        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}