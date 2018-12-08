<?php

namespace Altek\Accountant\Contracts;

interface ContextResolver
{
    /**
     * Resolve the current context.
     *
     * @return int
     */
    public static function resolve(): int;
}
