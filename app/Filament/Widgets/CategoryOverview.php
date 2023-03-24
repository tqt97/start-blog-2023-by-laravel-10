<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class CategoryOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = '2';

    protected function getCards(): array
    {
        $categories = Category::select(DB::raw('
            count(*) as total,
            SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active
        '))->first();
        return [
            Card::make(__('Total categories'), $categories->total)
                ->description(__('Total categories'))
                ->chart([3, 1])
                ->color('primary'),

            Card::make(__('Active categories'), $categories->active)
                ->description(__('Active categories'))
                ->chart([1, 3])
                ->color('success'),

            // Card::make(__('Admin categories'), $categories->admin)
            //     ->description(__('Admin categories'))
            //     ->chart([1, 1])
            //     ->color('secondary'),
        ];
    }
}
