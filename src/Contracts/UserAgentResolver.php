<?php

namespace Altek\Accountant\Contracts;

interface UserAgentResolver
{
    /**
     * Resolve the User Agent.
     *
     * @return string
     */
    public static function resolve(): ?string;
}
