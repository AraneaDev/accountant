<?php

namespace Altek\Accountant\Ciphers;

use Altek\Accountant\Exceptions\DecipherException;

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
        $length = mb_strlen($value);
        $tenth = ceil($length / 10);

        // Make sure single character strings get redacted
        $start = ($length > $tenth) ? ($length - $tenth) : 1;

        return str_pad(mb_substr($value, $start), $length, '-', STR_PAD_LEFT);
    }

    /**
     * {@inheritdoc}
     */
    public static function decipher($value)
    {
        throw new DecipherException('Value deciphering is not supported by this implementation', $value);
    }
}
