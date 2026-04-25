<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $settings = Setting::first();
            $view->with('siteTitle', $settings?->site_title ?? 'SnowmanBlog');
            $view->with('siteDescription', $settings?->site_description ?? '');
            $view->with('siteIcp', $settings?->icp ?? '');
            $view->with('siteAuthor', $settings?->author ?? '');
        });
    }
}