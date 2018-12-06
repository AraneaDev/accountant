<?php

namespace Altek\Accountant;

use Altek\Accountant\Console\LedgerDriverMakeCommand;
use Altek\Accountant\Contracts\Accountant;
use Illuminate\Support\ServiceProvider;

class AccountantServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $defer = true;

    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $config = __DIR__.'/../config/accountant.php';

        $this->mergeConfigFrom($config, 'accountant');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $config => base_path('config/accountant.php'),
            ], 'accountant-configuration');

            $migrations = __DIR__.'/../database/migrations/';

            $this->publishes([
                $migrations => database_path('migrations'),
            ], 'accountant-migrations');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands([
            LedgerDriverMakeCommand::class,
        ]);

        $this->app->singleton(Accountant::class, function ($app) {
            return new \Altek\Accountant\Accountant($app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [
            Accountant::class,
        ];
    }
}
