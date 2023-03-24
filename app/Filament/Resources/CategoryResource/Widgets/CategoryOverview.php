<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class CategoryOverview extends BaseWidget
{
    protected $listeners = ['updateCategoryOverview' => '$refresh'];

    protected function getCards(): array
    {

        $categories = Category::select(DB::raw('
            count(*) as total,
            SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN active = 0 THEN 1 ELSE 0 END) as inactive
        '))->first();
        return [
            Card::make(__('Total categories'), $categories->total)
                ->description(__('Total categories'))
                // ->chart([3, 1])
                ->color('primary'),

            Card::make(__('Active categories'), $categories->active)
                ->description(__('Active categories'))
                // ->chart([1, 1])
                ->color('success'),

                Card::make(__('Inactive categories'), $categories->inactive)
                ->description(__('Inactive categories'))
                // ->chart([1, 3])
                ->color('secondary'),

        ];
    }
}
