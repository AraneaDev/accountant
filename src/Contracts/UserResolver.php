<?php

namespace Altek\Accountant\Contracts;

interface UserResolver
{
    /**
     * Resolve the User.
     *
     * @return Identifiable
     */
    public static function resolve(): ?Identifiable;
}
