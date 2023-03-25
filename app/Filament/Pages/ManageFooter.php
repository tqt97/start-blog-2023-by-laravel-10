<?php

namespace App\Filament\Pages;

use App\Settings\FooterSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;

class ManageFooter extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = FooterSettings::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Settings';

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('site_name')
            ->label('site_name')
            ->required(),
            // Repeater::make('links')
            // ->schema([
            //     TextInput::make('label')->required(),
            //     TextInput::make('url')
            //         ->url()
            //         ->required(),
            // ]),
        ];
    }
}
