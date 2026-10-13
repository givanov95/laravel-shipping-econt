<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Econt;

use Givanov95\Shipping\DeliveryMethods;
use Givanov95\Shipping\Econt\Console\RecacheCitiesCommand;
use Illuminate\Support\ServiceProvider;

final class EcontServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/econt.php', 'econt');

        // Registered is not yet offered: the shop names the methods it offers.
        $this->callAfterResolving(DeliveryMethods::class, function (DeliveryMethods $methods): void {
            $methods->register('econt', EcontMethod::class, 'Econt');
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/econt.php' => config_path('econt.php')], 'econt-config');
            $this->commands([RecacheCitiesCommand::class]);
        }
    }
}
