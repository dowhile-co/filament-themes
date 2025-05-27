<?php

namespace Hasnayeen\Themes\Themes;

use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Contracts\Theme;

class AndreiaNord implements Theme
{
    public static function getName(): string
    {
        return 'andreia-nord';
    }

    public static function getPath(): string
    {
        return __DIR__ . '/../../resources/dist/andreia-nord.css';
    }

    public function getThemeColor(): array
    {
        return [
            'danger' => Color::hex('#bf616a'), // nord11
            'info' => Color::hex('#81a1c1'), // nord9
            'primary' => Color::hex('#88c0d0'), // nord8
            'secondary' => Color::hex('#5e81ac'), // nord10
            'success' => Color::hex('#a3be8c'), // nord14
            'warning' => Color::hex('#ebcb8b'), // nord13
        ];
    }

    public function getPrimaryColor(): array
    {
        return [
            'primary' => [
                50 => '#eceff4',  // nord6 - snow storm
                100 => '#e5e9f0', // nord5 - snow storm
                200 => '#d8dee9', // nord4 - snow storm
                300 => '#a7b1c5',
                400 => '#8c9ab3',
                500 => '#71829b',
                600 => '#4c566a', // nord3 - polar night
                700 => '#434c5e', // nord2 - polar night
                800 => '#3b4252', // nord1 - polar night
                900 => '#2e3440', // nord0 - polar night
                950 => '#232831',
            ]
        ];
    }
}
