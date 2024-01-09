<?php

declare(strict_types=1);

namespace Loffel\Subscriptions\Providers;

use Loffel\Subscriptions\Models\Plan;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Loffel\Subscriptions\Models\PlanFeature;
use Loffel\Subscriptions\Models\PlanSubscription;
use Loffel\Subscriptions\Models\PlanSubscriptionUsage;
use Loffel\Subscriptions\Console\Commands\MigrateCommand;
use Loffel\Subscriptions\Console\Commands\PublishCommand;
use Loffel\Subscriptions\Console\Commands\RollbackCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class,
        PublishCommand::class,
        RollbackCommand::class,
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            realpath(__DIR__ . '/../../config/config.php'), 'loffel.subscriptions'
        );

        // Bind eloquent models to IoC container
        $this->registerModels([
            'loffel.subscriptions.plan' => Plan::class,
            'loffel.subscriptions.plan_feature' => PlanFeature::class,
            'loffel.subscriptions.plan_subscription' => PlanSubscription::class,
            'loffel.subscriptions.plan_subscription_usage' => PlanSubscriptionUsage::class,
        ]);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            realpath(__DIR__ . '/../../config/config.php') => config_path('laravel-subscriptions.php'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands(
                $this->commands
            );
        }
    }
}
