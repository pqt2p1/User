<?php

namespace Pqt2p1\User;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Notifications\ResetPassword;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return "http://localhost:8000/password/reset-password/"."?token=$token&email={$notifiable->getEmailForPasswordReset()}";
        });
        
        $this->mergeConfigFrom(__DIR__ . '/../config/User.php', 'user');

        $this->publishConfig();

        // $this->loadViewsFrom(__DIR__.'/resources/views', 'user');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        });
    }

    /**
    * Get route group configuration array.
    *
    * @return array
    */
    private function routeConfiguration()
    {
        return [
            'namespace'  => "Pqt2p1\User\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register facade
        $this->app->singleton('user', function () {
            return new User;
        });
    }

    /**
     * Publish Config
     *
     * @return void
     */
    public function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/User.php' => config_path('User.php'),
            ], 'config');
        }
    }
}
