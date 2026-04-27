<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use App\Models\Link;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('siteTitle', Setting::get('site_title', 'SnowmanBlog'));
            $view->with('siteDescription', Setting::get('site_description', ''));
            $view->with('siteIcp', Setting::get('site_icp', ''));
            $view->with('siteAuthor', Setting::get('site_author', ''));
            $view->with('links', Link::where('is_visible', true)->orderBy('sort_order')->get());
        });
    }
}