<?php

namespace Hasnayeen\Themes\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;
use Hasnayeen\Themes\Contracts\HasOnlyDarkMode;
use Hasnayeen\Themes\Contracts\HasOnlyLightMode;
use Hasnayeen\Themes\Filament\Pages\Themes as ThemesPage;
use Hasnayeen\Themes\Themes;
use Hasnayeen\Themes\Themes\DefaultTheme;
use Hasnayeen\Themes\Themes\Sunset;
use Hasnayeen\Themes\Themes\Dracula;
use Hasnayeen\Themes\Themes\Nord;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Themes $themes */
        $themes = app(Themes::class);
        $panel = Filament::getCurrentPanel();

        if (! $panel->hasPlugin('themes')) {
            return $next($request);
        }

        /**
         * Important for Laravel Octane!
         *
         * Check if item already exists before adding it
         * to the menu items.
         */
        if (! isset($panel->getUserMenuItems()[__('themes::themes.themes')])) {
            $panel->userMenuItems(
                ThemesPlugin::canView() ?
                    [
                        __('themes::themes.themes') => MenuItem::make('Themes')
                            ->label(fn() => __('themes::themes.themes'))
                            ->icon(config('themes.icon'))
                            ->url(ThemesPage::getUrl()),
                    ] : []
            );
        }

        FilamentColor::register($themes->getCurrentThemeColor());

        $currentTheme = $themes->getCurrentTheme();
        FilamentAsset::register([
            match (get_class($currentTheme)) {
                Sunset::class => Css::make(Sunset::getName(), Sunset::getPath()),
                Dracula::class => Css::make(Dracula::getName(), Dracula::getPath()),
                Nord::class => Css::make(Nord::getName(), Nord::getPath()),
                default => Css::make(DefaultTheme::getName(), DefaultTheme::getPath()),
            },
        ], 'hasnayeen/themes');

        if (! $panel->hasDarkModeForced()) {
            $panel->darkMode(! $currentTheme instanceof HasOnlyLightMode, $currentTheme instanceof HasOnlyDarkMode);
        }

        if ($currentTheme instanceof CanModifyPanelConfig) {
            $currentTheme->modifyPanelConfig($panel);
        }

        // Add theme name as class to body element
        $bodyClass = "filament-themes-{$currentTheme::getName()}";
        FilamentAsset::register([
            Js::make('themes-body-class')
                ->html("<script>
                    // Add class on initial page load
                    document.addEventListener('DOMContentLoaded', function() {
                        document.body.classList.add('{$bodyClass}');
                    });
                    // Add class on Livewire page navigations (SPA mode)
                    document.addEventListener('livewire:navigated', function() {
                        document.body.classList.add('{$bodyClass}');
                    });
                </script>"),
        ], 'hasnayeen/themes');

        return $next($request);
    }
}
