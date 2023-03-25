<?php

namespace App\Providers;

use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

class FilamentFabricatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //Add custom tags (like `<meta>` & `<link>`)
        FilamentFabricator::pushMeta([
            new HtmlString('<link rel="manifest" href="/site.webmanifest" />'),
        ]);

        //Register scripts
        // FilamentFabricator::registerScripts([
        //     'https://unpkg.com/browse/tippy.js@6.3.7/dist/tippy.esm.js', //external url
        //     mix('js/app.js'), //laravel-mix
        //     app(Vite::class)('resources/css/app.js'), //vite
        //     asset('js/app.js'), // asset from public folder
        // ]);

        //Register styles
        FilamentFabricator::registerStyles([
            // 'https://unpkg.com/tippy.js@6/dist/tippy.css', //external url
            // mix('css/app.css'), //laravel-mix
            app(Vite::class)('resources/css/app.css'), //vite
            // asset('css/app.css'), // asset from public folder
        ]);

        FilamentFabricator::favicon(asset('favicon.ico'));
    }
}
