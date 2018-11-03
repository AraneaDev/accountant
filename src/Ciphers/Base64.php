<?php

namespace Altek\Accountant\Ciphers;

class Base64 implements \Altek\Accountant\Contracts\Cipher
{
    /**
     * {@inheritdoc}
     */
    public static function isOneWay(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function cipher($value)
    {
        return base64_encode($value);
    }

    /**
     * {@inheritdoc}
     */
    public static function decipher($value)
    {
        return base64_decode($value);
    }
}
