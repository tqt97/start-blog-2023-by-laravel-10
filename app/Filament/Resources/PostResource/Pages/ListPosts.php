<?php

namespace App\Filament\Resources\PostResource\Pages;

use Filament\Pages\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PostResource\Widgets\PostOverview;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PostOverview::class,
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            PostOverview::class,
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
