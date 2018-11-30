<?php

namespace Altek\Accountant\Signers;

class LedgerSigner implements \Altek\Accountant\Contracts\LedgerSigner
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
}
