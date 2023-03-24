<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PostOverview extends BaseWidget
{
    protected int | string | array $columnSpan = '2';
    protected static ?int $sort = 2;


    protected function getCards(): array
    {
        $posts = Post::select(DB::raw('
            count(*) as total,
            SUM(CASE WHEN published_at != "" AND active = 1 THEN 1 ELSE 0 END) as active
        '))->first();
        return [
            Card::make(__('Total posts'), $posts->total)
            ->description(__('Total posts'))
            ->chart([3, 1])
            ->color('primary'),

        Card::make(__('Active posts'), $posts->active)
            ->description(__('Active posts'))
            ->chart([1, 3])
            ->color('success'),
        ];
    }
}
