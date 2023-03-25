<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Generators\DatePathGenerator;
use Awcodes\Curator\Generators\UserPathGenerator;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

use Illuminate\Support\Str;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

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
                    Forms\Components\Textarea::make('summary')->translateLabel()
                        ->hint(function (?string $state) {
                            $readingMinutes = Str::readingMinutes($state);
                            return $readingMinutes . ' min read';
                        })
                        ->lazy(),
                    Forms\Components\RichEditor::make('content')
                        ->translateLabel()
                        ->hint(function (?string $state) {
                            $readingMinutes = Str::readingMinutes($state);
                            return $readingMinutes . ' min read';
                        })
                        ->lazy(),
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
                ])->columnSpan(4),
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('meta_title')->translateLabel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('meta_description')->translateLabel()
                        ->maxLength(255),
                ])->columnSpan(12),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->successNotificationTitle('Add post success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
