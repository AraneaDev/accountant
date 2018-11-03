<?php

namespace Altek\Accountant\Ciphers;

use Altek\Accountant\Exceptions\AccountantException;

class Bleach implements \Altek\Accountant\Contracts\Cipher
{
    /**
     * {@inheritdoc}
     */
    public static function isOneWay(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function cipher($value)
    {
        $length = strlen($value);
        $tenth = ceil($length / 10);

        // Make sure single character strings get redacted
        $start = ($length > $tenth) ? ($length - $tenth) : 1;

        return str_pad(substr($value, $start), $length, '-', STR_PAD_LEFT);
    }

    /**
     * {@inheritdoc}
     */
    public static function decipher($value)
    {
        throw new AccountantException('This Cipher implementation does not support deciphering');
    }
}
