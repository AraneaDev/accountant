<?php

namespace Altek\Accountant\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserResolver
{
    /**
     * Resolve the User.
     *
     * @return Authenticatable
     */
    public static function resolve(): ?Authenticatable;
}
