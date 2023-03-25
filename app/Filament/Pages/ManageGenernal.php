<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;

class ManageGenernal extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Settings';

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('app_name')
                ->label('app_name')
                ->required(),
            TextInput::make('author')
                ->label('author')
                ->required(),
            TextInput::make('logo')
                ->label('logo')
                ->required(),
        ];
    }
}
