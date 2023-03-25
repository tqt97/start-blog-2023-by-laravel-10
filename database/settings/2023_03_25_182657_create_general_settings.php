<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_name', 'laravel');
        $this->migrator->add('general.author', 'tuantq');
        $this->migrator->add('general.logo', 'logo.svg');
    }
};
