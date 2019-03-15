<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Integration;

use Altek\Accountant\Tests\AccountantTestCase;

class CommandTest extends AccountantTestCase
{
    /**
     * @test
     *
     * @dataProvider makeCommandProvider
     *
     * @param string $relativePath
     * @param string $command
     * @param string $argument
     */
    public function itSuccessfullyCreatesClassSkeleton(string $relativePath, string $command, string $argument): void
    {
        $filePath = $this->app->path($relativePath);

        $this->assertFileNotExists($filePath);

        $this->artisan($command, [
            'name' => $argument,
        ]);

        $this->assertFileExists($filePath);

        $this->assertTrue(\unlink($filePath));
    }

    /**
     * @return array
     */
    public function makeCommandProvider(): array
    {
        return [
            'make:cipher' => [
                'Ciphers/TestCipher.php',
                'make:cipher',
                'TestCipher',
            ],

            'make:context-resolver' => [
                'Resolvers/TestContextResolver.php',
                'make:context-resolver',
                'TestContextResolver',
            ],

            'make:ip-address-resolver' => [
                'Resolvers/TestIpAddressResolver.php',
                'make:ip-address-resolver',
                'TestIpAddressResolver',
            ],

            'make:ledger-driver' => [
                'LedgerDrivers/TestDriver.php',
                'make:ledger-driver',
                'TestDriver',
            ],

            'make:notary' => [
                'TestNotary.php',
                'make:notary',
                'TestNotary',
            ],

            'make:url-resolver' => [
                'Resolvers/TestUrlResolver.php',
                'make:url-resolver',
                'TestUrlResolver',
            ],

            'make:user-agent-resolver' => [
                'Resolvers/TestUserAgentResolver.php',
                'make:user-agent-resolver',
                'TestUserAgentResolver',
            ],

            'make:user-resolver' => [
                'Resolvers/TestUserResolver.php',
                'make:user-resolver',
                'TestUserResolver',
            ],
        ];
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

        $this->assertTrue(\unlink($configurationFilePath));
    }

    /**
     * @test
     */
    public function itPublishesMigrationFiles(): void
    {
        $migrationFilePath01 = $this->app->databasePath('migrations/2018_11_21_000001_create_ledgers_table.php');

        $this->assertFileNotExists($migrationFilePath01);

        $this->artisan('vendor:publish', [
            '--tag' => 'accountant-migrations',
        ]);

        $this->assertFileExists($migrationFilePath01);

        $this->assertTrue(\unlink($migrationFilePath01));
    }
}
