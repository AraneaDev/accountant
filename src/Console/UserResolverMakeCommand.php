<?php

declare(strict_types=1);

namespace Altek\Accountant\Console;

use Illuminate\Console\GeneratorCommand;

class UserResolverMakeCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:user-resolver';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new User resolver';

    /**
     * {@inheritdoc}
     */
    protected $type = 'UserResolver';

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/UserResolver.stub';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Resolvers';
    }
}
