<?php

declare(strict_types=1);

namespace Altek\Accountant\Console;

use Illuminate\Console\GeneratorCommand;

class MakeNotaryCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:notary';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new Notary implementation';

    /**
     * {@inheritdoc}
     */
    protected $type = 'Notary';

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/Notary.stub';
    }
}