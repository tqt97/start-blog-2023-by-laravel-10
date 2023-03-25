<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('footer.site_name', 'Tuantq');
        $this->migrator->add('footer.site_active', true);
    }
};
