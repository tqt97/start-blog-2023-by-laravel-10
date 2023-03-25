<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers\TagsRelationManager;
use App\Models\Post;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    protected static ?string $recordTitleAttribute = 'title';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(512)
                        ->unique(ignorable: fn ($record) => $record)
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('slug', Str::slug($state));
                        })
                        ->autofocus()
                        ->translateLabel(),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignorable: fn ($record) => $record)
                        ->maxLength(512)
                        ->translateLabel()
                        ->helperText('Use to define unique of post. Default set when change value of name. You can customize it'),
                    Forms\Components\Textarea::make('summary')->translateLabel(),
                    Forms\Components\RichEditor::make('content')->translateLabel(),
                ])->columnSpan(8),

                Forms\Components\Card::make()->schema([
                    CuratorPicker::make('thumbnail')
                        ->label('Image')
                        ->buttonLabel('Upload image')
                        ->color('secondary') // defaults to primary
                        ->outlined(false) // defaults to true
                        ->size('md') // defaults to md
                        ->pathGenerator(DatePathGenerator::class | UserPathGenerator::class) // see path generators below
                        ->preserveFilenames(),
                    Forms\Components\Toggle::make('active')->translateLabel()
                        ->required()
                        ->onIcon('heroicon-s-lightning-bolt')
                        ->offIcon('heroicon-s-user')
                        ->onColor('success')
                        ->offColor('secondary'),
                    Forms\Components\DateTimePicker::make('published_at')->translateLabel(),
                    Forms\Components\Select::make('categories')
                        ->translateLabel()
                        ->required()
                        ->multiple()
                        ->relationship('categories', 'name')
                        ->preload()
                        ->searchable(),
                    Forms\Components\Select::make('tags')
                        ->translateLabel()
                        ->multiple()
                        ->relationship('tags', 'name')
                        ->preload()
                        ->searchable(),
                ])->columnSpan(4),
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('meta_title')->translateLabel()
                        ->hint(fn ($state, $component) => 'left: ' . $component->getMaxLength() - strlen($state) . ' characters')
                        ->lazy()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('meta_description')->translateLabel()
                        ->hint(fn ($state, $component) => 'left: ' . $component->getMaxLength() - strlen($state) . ' characters')
                        ->lazy()
                        ->maxLength(255),
                ])->columnSpan(12),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('active')
                    ->extraAttributes(['class' => 'flex justify-center'])
                    ->boolean()
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                TernaryFilter::make('active')
                    ->label('Active')
                    ->indicator('Active'),
                SelectFilter::make('user')->label('Author')->relationship('user', 'name'),
                SelectFilter::make('categories')->label('Category')->relationship('categories', 'name'),
                Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from'),
                        Forms\Components\DatePicker::make('published_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['published_from'] || !$data['published_until']) {
                            return null;
                        }

                        return 'Published from ' . Carbon::parse($data['published_from'])->toFormattedDateString() . ' to ' . Carbon::parse($data['published_until'])->toFormattedDateString();
                    }),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Created from'),
                        DatePicker::make('until')->label('Created until'),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Created from ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Created until ' . Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->excludeAttributes(['title', 'slug', 'parent_id'])
                        ->form([
                            Forms\Components\TextInput::make('title')->required()
                                ->unique()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            Forms\Components\TextInput::make('slug')->translateLabel()
                                ->helperText('Use to define unique of post. Default set when change value of name. You can customize it')
                                ->required()
                                ->unique()
                                ->maxLength(512),
                            Forms\Components\Select::make('categories')
                                ->translateLabel()
                                ->required()
                                ->multiple()
                                ->relationship('categories', 'name')
                                ->preload()
                                ->searchable(),
                        ])
                        ->beforeReplicaSaved(function (Post $replica, array $data): void {
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
                Tables\Actions\BulkAction::make('exportAllAsJson')
                    ->label(__('Export All'))
                    ->icon('heroicon-s-download')
                    ->action(function (PostResource $records) {
                        $archive = new \ZipArchive;
                        $archive->open('file.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                        foreach ($records as $record) {
                            $title = Str::slug($record->title, '_') . '.json';
                            $return = $record->attributesToArray();
                            $content = json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                            $archive->addFromString($title, $content);
                        }
                        $archive->close();
                        return response()->download('file.zip');
                    })
                    ->deselectRecordsAfterCompletion()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TagsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
