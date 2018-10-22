<?php

namespace Altek\Accountant\Tests\Integration;

use Altek\Accountant\Tests\AccountantTestCase;

class CommandTest extends AccountantTestCase
{
    /**
     * @test
     */
    public function itWillCreateALedgerDriver(): void
    {
        $driverFilePath = sprintf('%s/LedgerDrivers/TestDriver.php', $this->app->path());

        $this->artisan('make:ledger-driver', [
            'name' => 'TestDriver',
        ]);

        $this->assertFileExists($driverFilePath);

        $this->assertTrue(unlink($driverFilePath));
    }
}
