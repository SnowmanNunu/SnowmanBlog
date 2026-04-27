<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\Link;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('siteTitle', Cache::tags(['settings'])->remember('setting:site_title', 86400, fn () => Setting::get('site_title', 'SnowmanBlog')));
            $view->with('siteDescription', Cache::tags(['settings'])->remember('setting:site_description', 86400, fn () => Setting::get('site_description', '')));
            $view->with('siteIcp', Cache::tags(['settings'])->remember('setting:site_icp', 86400, fn () => Setting::get('site_icp', '')));
            $view->with('siteAuthor', Cache::tags(['settings'])->remember('setting:site_author', 86400, fn () => Setting::get('site_author', '')));
            $view->with('links', Cache::tags(['links'])->remember('links:visible', 3600, fn () => Link::where('is_visible', true)->orderBy('sort_order')->get()));
        });
    }
}
