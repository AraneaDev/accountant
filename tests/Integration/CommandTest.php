<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Integration;

use Altek\Accountant\Tests\AccountantTestCase;

class CommandTest extends AccountantTestCase
{
    /**
     * @test
     */
    public function itCreatesLedgerDriverSkeleton(): void
    {
        $driverFilePath = $this->app->path('LedgerDrivers/TestDriver.php');

        $this->assertFileNotExists($driverFilePath);

        $this->artisan('make:ledger-driver', [
            'name' => 'TestDriver',
        ]);

        $this->assertFileExists($driverFilePath);

        $this->assertTrue(unlink($driverFilePath));
    }

    /**
     * @test
     */
    public function itPublishesConfigurationFile(): void
    {
        $configurationFilePath = $this->app->configPath('accountant.php');

        $this->assertFileNotExists($configurationFilePath);

        $this->artisan('vendor:publish', [
            '--tag' => 'accountant-configuration',
        ]);

        $this->assertFileExists($configurationFilePath);

        $this->assertTrue(unlink($configurationFilePath));
    }

    /**
     * @test
     */
    public function itPublishesMigrationFile(): void
    {
        $migrationFilePath = $this->app->databasePath('migrations/2018_11_21_000001_create_ledgers_table.php');

        $this->assertFileNotExists($migrationFilePath);

        $this->artisan('vendor:publish', [
            '--tag' => 'accountant-migrations',
        ]);

        $this->assertFileExists($migrationFilePath);

        $this->assertTrue(unlink($migrationFilePath));
    }
}
