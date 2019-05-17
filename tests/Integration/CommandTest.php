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
            'accountant:cipher' => [
                'Ciphers/TestCipher.php',
                'accountant:cipher',
                'TestCipher',
            ],

            'accountant:context-resolver' => [
                'Resolvers/TestContextResolver.php',
                'accountant:context-resolver',
                'TestContextResolver',
            ],

            'accountant:ip-address-resolver' => [
                'Resolvers/TestIpAddressResolver.php',
                'accountant:ip-address-resolver',
                'TestIpAddressResolver',
            ],

            'accountant:ledger-driver' => [
                'LedgerDrivers/TestDriver.php',
                'accountant:ledger-driver',
                'TestDriver',
            ],

            'accountant:notary' => [
                'TestNotary.php',
                'accountant:notary',
                'TestNotary',
            ],

            'accountant:url-resolver' => [
                'Resolvers/TestUrlResolver.php',
                'accountant:url-resolver',
                'TestUrlResolver',
            ],

            'accountant:user-agent-resolver' => [
                'Resolvers/TestUserAgentResolver.php',
                'accountant:user-agent-resolver',
                'TestUserAgentResolver',
            ],

            'accountant:user-resolver' => [
                'Resolvers/TestUserResolver.php',
                'accountant:user-resolver',
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
