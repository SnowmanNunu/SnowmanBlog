<?php

namespace AppProviders;

use AppModelsSetting;
use IlluminateSupportFacadesView;
use IlluminateSupportServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.blog', function ($view) {
            $view->with('siteTitle', Setting::get('site_title', 'SnowmanBlog'));
            $view->with('siteIcp', Setting::get('site_icp', ''));
            $view->with('siteAuthor', Setting::get('site_author', 'SnowmanNunu'));
            $view->with('siteDescription', Setting::get('site_description', ''));
        });
    }
}