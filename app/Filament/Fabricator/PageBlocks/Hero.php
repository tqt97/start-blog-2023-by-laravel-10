<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Awcodes\Curator\Components\Forms\CuratorPicker;


class Hero extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('hero')
            ->schema([
                TextInput::make('title'),
                Textarea::make('description'),
                Repeater::make('buttons')
                    ->schema([
                        TextInput::make('button_text'),
                    ]),
                CuratorPicker::make('image')
                    ->label('Hero Image')
                    ->buttonLabel('Upload image')
                    ->color('primary') // defaults to primary
                    ->outlined(false) // defaults to true
                    ->size('md') // defaults to md
                    ->pathGenerator(DatePathGenerator::class | UserPathGenerator::class) // see path generators below
                    ->preserveFilenames()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
