<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\Widgets\CategoryOverview;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Blog';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')->label(__('Name'))
                        ->autofocus()
                        ->translateLabel()
                        ->minLength(3)
                        ->maxLength(255)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('slug', Str::slug($state));
                        })
                        ->unique(ignorable: fn ($record) => $record),
                    Forms\Components\TextInput::make('slug')->translateLabel()
                        ->helperText('Use to define unique of category. Default set when change value of name. You can customize it')
                        ->required()
                        ->maxLength(512)
                        ->unique(ignorable: fn ($record) => $record),
                    Forms\Components\Textarea::make('description')->label('Description'),
                ])->columnSpan(8),
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('parent_id')
                        ->label('Categories')->translateLabel()
                        ->helperText('Default is null. It\'s root category.')
                        ->relationship('parent', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\FileUpload::make('banner')->translateLabel()
                        ->helperText('Default size image is 1200px x 600px.')
                        ->preserveFilenames()
                        ->image()
                        // ->minSize(512)
                        // ->maxSize(1024)
                        // ->imageResizeMode('cover')
                        // ->imageCropAspectRatio('16:9')
                        // ->imageResizeTargetWidth('1920')
                        // ->imageResizeTargetHeight('1080')
                        // ->imagePreviewHeight('250')
                        ->loadingIndicatorPosition('left')
                        // ->panelAspectRatio('2:1')
                        // ->panelLayout('integrated')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left'),
                    Forms\Components\Toggle::make('active')->translateLabel()
                        ->onIcon('heroicon-s-lightning-bolt')
                        ->offIcon('heroicon-s-user')
                        ->onColor('success')
                        ->offColor('danger')
                        ->required(),
                ])->columnSpan(4),
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->translateLabel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('meta_description')
                        ->translateLabel()
                        ->maxLength(255),
                ])->columnSpan(12),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner')->toggleable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                // Tables\Columns\ToggleColumn::make('active'),
                Tables\Columns\IconColumn::make('active')->boolean()
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->dateTime('d-m-Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime('d-m-Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                TernaryFilter::make('active')
                    ->label('Active')
                    ->indicator('Active'),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['created_from'] || !$data['created_until']) {
                            return null;
                        }
                        return 'Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString() . ' to ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->excludeAttributes(['name', 'slug'])
                        ->form([
                            TextInput::make('name')->required()
                                ->unique()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            Forms\Components\TextInput::make('slug')->translateLabel()
                                ->helperText('Use to define unique of category. Default set when change value of name. You can customize it')
                                ->required()
                                ->unique()
                                ->maxLength(512),
                        ])
                        ->beforeReplicaSaved(function (Category $replica, array $data): void {
                            $replica->fill($data);
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
}
