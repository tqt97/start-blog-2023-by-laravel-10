<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\HtmlString;

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
            // Filament::registerViteTheme('resources/css/filament.css');
            // Using Vite
            Filament::registerTheme(
                app(Vite::class)('resources/css/filament.css'),
            );

            // Filament::registerScripts([
            //     asset('js/my-script.js'),
            // ]);
            // Filament::registerScripts([
            //     'https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-tooltip@0.x.x/dist/cdn.min.js',
            // ], true);
            // Filament::registerStyles([
            //     'https://unpkg.com/tippy.js@6/dist/tippy.css',
            //     asset('css/my-styles.css'),
            // ]);

            // Filament::pushMeta([
            //     new HtmlString('<link rel="manifest" href="/site.webmanifest" />'),
            // ]);

            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Blog')
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),
            ]);

        });
    }
}
