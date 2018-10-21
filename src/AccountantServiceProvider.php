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
        $migration = __DIR__.'/../database/migrations/ledgers.stub';

        $this->publishes([
            $config => base_path('config/accountant.php'),
        ], 'config');

        $this->publishes([
            $migration => database_path(sprintf('migrations/%s_create_ledgers_table.php', date('Y_m_d_His'))),
        ], 'migrations');

        $this->mergeConfigFrom($config, 'accountant');
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
