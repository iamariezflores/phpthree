<?php

namespace iamariezflores\phpthree;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PhpThreeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('phpthree', function() {
            return new PhpThree();    
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/phpthree.php' => config_path("phpthree.php"),
        ], "phpthree-config");

        app()->singleton('threeJsInjected', function () {
            return false;
        });
    
        Blade::directive('threeJsCdn', function () {
            if (!app('threeJsInjected')) {
                app()->instance('threeJsInjected', true);
                return '<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>';
            }
    
            return '';
        });
    }
}
