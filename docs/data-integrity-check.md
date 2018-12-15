# Data Integrity Check
This section describes the available mechanisms for checking data integrity.

## Notary
Like with the profession bearing the same name, the `Notary` class handles signing and validation.

The bundled `Notary` implementation generates signatures using the `SHA-512` algorithm.

If needed, the default implementation can be replaced with a custom one. 

> **TIP:** You cab generate a skeleton `Notary` class with the `php artisan make:notary <class name>` command.

### Example
A custom `Notary` implementation using the **Argon2** algorithm.

```php
<?php

namespace App\Support;

class Notary implements \Altek\Accountant\Contracts\Notary
{
    /**
     * Determine if an array is indexed.
     *
     * @param array $data
     *
     * @return bool
     */
    public static function isIndexed(array $data): bool
    {
        return array_keys($data) === range(0, count($data) - 1);
    }

    /**
     * Sort a multidimensional array.
     *
     * @param array $data
     *
     * @return void
     */
    public static function sort(array &$data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value) && $value) {
                static::sort($data[$key]);
            }
        }

        static::isIndexed($data) ? sort($data) : ksort($data);
    }
    
    /**
     * {@inheritdoc}
     */
    public static function sign(array $data): string
    {
        static::sort($data);

        return password_hash(json_encode($data, JSON_NUMERIC_CHECK), PASSWORD_ARGON2I);
    }

    /**
     * {@inheritdoc}
     */
    public static function validate(array $data, string $signature): bool
    {
        static::sort($data);

        return password_verify(json_encode($data, JSON_NUMERIC_CHECK), $signature);
    }
}
```

Update the `accountant.notary` configuration value with:

```php
return [

    // ...

    'notary' => App\Support\Notary::class,

    // ...
];
```

> **NOTICE:** The default `signature` column type is set to `VARCHAR(255)`, so keep that in mind when generating signatures with other algorithms.

## Checking Ledger model integrity with isTainted()
During the `Ledger` creation process, a signature is generated and stored along with the rest of the data.

### Validation process
Firstly, the method will check if the timestamps match, since there shouldn't be a discrepancy between the created and updated date/time.
If the previous check passes, it will then validate the stored signature against the data.

The method will return `true` when a record has been tampered with, otherwise `false`.

### Usage example
```php
// Get the first Article created
$article = Article::first();

// Get the first Ledger
$ledger = $article->ledgers()->first();

if ($ledger->isTainted()) {
    // Take action
}
```

## Checking Recordable model integrity with isCurrentStateReachable()
By default, a `Ledger` record will be created each time a `Recordable` model is created and subsequently modified.

### Validation process
In a nutshell, the method will check if the current state can be reached by going through all the recorded history, comparing the reached state to the current one.

This method also returns a `bool` value.

### Usage example
```php
// Get the first Article created
$article = Article::first();

if (!$ledger->isCurrentStateReachable()) {
    // The current state could not be reached
}
```

> **CAVEAT:** Setting a `Ledger` [threshold](recordable-configuration.md#ledger-threshold), excluding [events](recordable-configuration.md#events) or configuring [one way](ciphers.md#bleach-cipher) ciphers will affect `Recordable` state checks!
