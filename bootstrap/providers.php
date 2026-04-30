<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\ViewComposerServiceProvider;

return [
    AppServiceProvider::class,
    ViewComposerServiceProvider::class,
    AdminPanelProvider::class,
];
