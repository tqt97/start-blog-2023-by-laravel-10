<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Closure;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPost extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';
    protected function getTableQuery(): Builder
    {
        return Post::query()
            ->latest()
            ->take(5);
    }

    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('thumbnail')
                ->sortable()
                ->searchable(),
            TextColumn::make('title')
                ->sortable()
                ->searchable()
                ->toggleable()
                ->wrap(),
            TextColumn::make('published_at')
                ->sortable()
                ->searchable()
                ->toggleable(),
            TextColumn::make('created_at')
                ->sortable()
                ->searchable()
                ->toggleable(),
        ];
    }
    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
