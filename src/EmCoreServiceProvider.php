<?php
namespace ME\EmCore;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use ME\EmCore\Console\Commands\TriggerNotifyPeopleCommand;

class EmCoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'em_core');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'em_core');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        // $this->publishes([
        //     __DIR__.'/Config' => config_path('em_core'),
        // ], 'em_core-config');
        $this->publishes([
            __DIR__ . '/public' => public_path('/'),
        ], 'emcore-assets');

        if ($this->app->runningInConsole()) {
            $this->commands([
                TriggerNotifyPeopleCommand::class,
            ]);
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('emcore:notify-people-trigger')->dailyAt('00:00');
        });
    }

    public function register()
    {
        if (file_exists(__DIR__ . '/Config/sidebar.php')) {
            $this->mergeConfigFrom(__DIR__ . '/Config/sidebar.php', 'sidebar');
        }

        if (file_exists(__DIR__ . '/Config/permissions.php')) {
            $this->mergeConfigFrom(__DIR__ . '/Config/permissions.php', 'permissions');
        }
    }
}
