<?php

namespace Altek\Accountant\Console;

use Illuminate\Console\GeneratorCommand;

class LedgerDriverMakeCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:ledger-driver';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new Ledger driver';

    /**
     * {@inheritdoc}
     */
    protected $type = 'LedgerDriver';

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../drivers/driver.stub';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\LedgerDrivers';
    }
}
