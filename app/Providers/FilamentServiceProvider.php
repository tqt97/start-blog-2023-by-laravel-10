<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\HtmlString;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;

class FilamentServiceProvider extends ServiceProvider
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
        Filament::serving(function () {
            // Using Vite
            Filament::registerTheme(
                app(Vite::class)('resources/css/filament.css'),
            );

            FilamentFabricator::pushMeta([
                new HtmlString('<link rel="manifest" href="/site.webmanifest" />'),
            ]);

            //Register scripts
            // FilamentFabricator::registerScripts([
            //     'https://unpkg.com/browse/tippy.js@6.3.7/dist/tippy.esm.js', //external url
            //     // mix('js/app.js'), //laravel-mix
            //     app(Vite::class)('resources/css/app.js'), //vite
            //     asset('js/app.js'), // asset from public folder
            // ]);

            //Register styles
            FilamentFabricator::registerStyles([
                // 'https://unpkg.com/tippy.js@6/dist/tippy.css', //external url
                // mix('css/app.css'), //laravel-mix
                app(Vite::class)('resources/css/app.css'), //vite
                asset('css/app.css'), // asset from public folder
            ]);

            // FilamentFabricator::favicon(asset('favicon.ico'));

            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Blog')
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog')
                    ->collapsed(),
            ]);
        });
    }
}
