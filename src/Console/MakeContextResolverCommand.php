<?php

declare(strict_types=1);

namespace Altek\Accountant\Console;

use Illuminate\Console\GeneratorCommand;

class MakeContextResolverCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:context-resolver';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new Context resolver';

    /**
     * {@inheritdoc}
     */
    protected $type = 'ContextResolver';

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/ContextResolver.stub';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Resolvers';
    }
}
