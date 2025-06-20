<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentColor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
        // 'danger' => Color::Red,
        // 'primary' => Color::Blue,
        'danger'  => Color::hex('#DC143C'),
        'primary' => Color::hex('#001F54'),
        'info' => Color::hex('#888B8D'),
        'gray' => Color::Zinc,
        'success' => Color::Green,
        'warning' => Color::Amber,
]);
    }
}
