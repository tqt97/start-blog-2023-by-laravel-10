<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PostOverview extends BaseWidget
{
    protected function getCards(): array
    {

        $posts = Post::select(DB::raw('
            count(*) as total,
            SUM(CASE WHEN active = 1 AND published_at != "" THEN 1 ELSE 0 END) as published,
            SUM(CASE WHEN active = 0 THEN 1 ELSE 0 END) as inactive
        '))->first();
        return [
            Card::make(__('Total posts'), $posts->total)
                ->description(__('Total posts'))
                // ->chart([3, 1])
                ->color('primary'),

            Card::make(__('Active posts'), $posts->published)
                ->description(__('Active posts'))
                // ->chart([1, 1])
                ->color('success'),

            Card::make(__('Inactive posts'), $posts->inactive)
                ->description(__('Inactive posts'))
                // ->chart([1, 3])
                ->color('secondary'),
        ];
    }
}
