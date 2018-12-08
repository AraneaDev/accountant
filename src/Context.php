<?php

namespace Altek\Accountant;

use Illuminate\Support\Facades\Config;

class Context
{
    public const TEST = 0b001;
    public const CLI  = 0b010;
    public const WEB  = 0b100;

    /**
     * Determine if a given context is valid.
     *
     * @param int $context
     *
     * @return bool
     */
    public static function isValid(int $context): bool
    {
        return (Config::get('accountant.contexts') & $context) !== 0b000;
    }
}
