<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Pages\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CategoryResource\Widgets\CategoryOverview;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public static function getWidgets(): array
    {
        return [
            CategoryOverview::class,
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            CategoryOverview::class,
        ];
    }
    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updateCategoryOverview');
        }
        // if (Str::of($name)->contains('tableFilters')) {
        //     $this->emit('updateCategoryOverview');
        // }
    }
}
