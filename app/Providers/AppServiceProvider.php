<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $disk = Setting::get('media_disk', config('filesystems.media_disk', 'public'));
                config()->set('filesystems.media_disk', $disk);

                if ($disk === 'oss') {
                    config()->set('filesystems.disks.oss.access_id', Setting::get('oss_access_key_id'));
                    config()->set('filesystems.disks.oss.access_secret', Setting::get('oss_access_key_secret'));
                    config()->set('filesystems.disks.oss.bucket', Setting::get('oss_bucket'));
                    config()->set('filesystems.disks.oss.endpoint', Setting::get('oss_endpoint'));
                    config()->set('filesystems.disks.oss.cdn_domain', Setting::get('oss_cdn_domain'));
                }

                if ($disk === 'cos') {
                    config()->set('filesystems.disks.cos.credentials.secret_id', Setting::get('cos_secret_id'));
                    config()->set('filesystems.disks.cos.credentials.secret_key', Setting::get('cos_secret_key'));
                    config()->set('filesystems.disks.cos.bucket', Setting::get('cos_bucket'));
                    config()->set('filesystems.disks.cos.region', Setting::get('cos_region'));
                    config()->set('filesystems.disks.cos.cdn', Setting::get('cos_cdn'));
                }

                if ($disk === 'qiniu') {
                    config()->set('filesystems.disks.qiniu.access_key', Setting::get('qiniu_access_key'));
                    config()->set('filesystems.disks.qiniu.secret_key', Setting::get('qiniu_secret_key'));
                    config()->set('filesystems.disks.qiniu.bucket', Setting::get('qiniu_bucket'));
                    config()->set('filesystems.disks.qiniu.domain', Setting::get('qiniu_domain'));
                }

                if ($disk === 's3') {
                    config()->set('filesystems.disks.s3.key', Setting::get('aws_access_key_id'));
                    config()->set('filesystems.disks.s3.secret', Setting::get('aws_secret_access_key'));
                    config()->set('filesystems.disks.s3.region', Setting::get('aws_default_region'));
                    config()->set('filesystems.disks.s3.bucket', Setting::get('aws_bucket'));
                    config()->set('filesystems.disks.s3.endpoint', Setting::get('aws_endpoint'));
                    config()->set('filesystems.disks.s3.url', Setting::get('aws_url'));
                }
            }
        } catch (\Throwable $e) {
            // Ignore if database is not available yet
        }
    }
}
