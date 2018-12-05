<?php

namespace Altek\Accountant;

class Notary implements Contracts\Notary
{
    /**
     * {@inheritdoc}
     */
    public static function sign(array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                ksort($data[$key]);
            }
        }

        ksort($data);

        return hash('sha512', json_encode($data, JSON_NUMERIC_CHECK));
    }

    /**
     * {@inheritdoc}
     */
    public static function validate(array $data, string $signature): bool
    {
        return static::sign($data) === $signature;
    }
}
