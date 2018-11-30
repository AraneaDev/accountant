<?php

namespace Altek\Accountant\Contracts;

interface LedgerSigner
{
    /**
     * Generate a signature based on the contents of an array.
     *
     * @param array $data
     *
     * @return string
     */
    public static function sign(array $data): string;
}
