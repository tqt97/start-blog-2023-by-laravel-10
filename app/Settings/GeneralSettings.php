<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $app_name;
    public string $logo;
    public string $author;



    public static function group(): string
    {
        return 'general';
    }
}
