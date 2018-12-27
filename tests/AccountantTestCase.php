<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests;

use Altek\Accountant\AccountantServiceProvider;
use Altek\Accountant\Context;
use Altek\Accountant\Resolvers\ContextResolver;
use Altek\Accountant\Resolvers\IpAddressResolver;
use Altek\Accountant\Resolvers\UrlResolver;
use Altek\Accountant\Resolvers\UserAgentResolver;
use Altek\Accountant\Resolvers\UserResolver;
use Carbon\Carbon;
use Orchestra\Testbench\TestCase;

abstract class AccountantTestCase extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Database
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Accountant
        $app['config']->set('accountant.resolvers', [
            'context'    => ContextResolver::class,
            'user'       => UserResolver::class,
            'url'        => UrlResolver::class,
            'ip_address' => IpAddressResolver::class,
            'user_agent' => UserAgentResolver::class,
        ]);

        $app['config']->set('accountant.contexts', Context::TEST | Context::CLI | Context::WEB);

        $app['config']->set('accountant.user.prefix', 'user');
        $app['config']->set('accountant.user.guards', [
            'web',
            'api',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->withFactories(__DIR__.'/database/factories');

        // Define an exact date/time to be always returned
        Carbon::setTestNow('2012-06-14 15:03:03');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            AccountantServiceProvider::class,
        ];
    }
}
