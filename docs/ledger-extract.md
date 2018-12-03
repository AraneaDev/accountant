# Ledger Extract
A `Ledger` is a snapshot of a `Recordable` model at a given point in time.
With the `extract()` method, an exact `Recordable` instance can be instantiated from a `Ledger`.

This method supports two modes of operation.

## Strict mode
This is the default, when the `extract()` method is called without arguments.

In order to recreate an exact `Recordable` instance in this mode, **ALL** properties must be available in their original form and the `Ledger` cannot be tainted.

### Usage example
```php
// ...

try {
    $article = Article::find(123);

    $snapshot = $article->ledgers()->first()->extract();

} catch (DecipherException $e) {
    // Handle potential exception while deciphering
} catch (AccountantException $e) {
    // Handle potential exception due to an invalid ledger, property or cipher implementation
}

// ...
```

> **CAVEAT:** The method will throw a `Altek\Accountant\Exceptions\DecipherException` if a property has been encoded by a **one way** cipher, which makes deciphering unfeasible.

## Soft mode
This is more easy-going approach, which will tolerate tainted ledgers and **one way** ciphered property values.
 
To use this mode, pass `false` as the only argument to the `extract()` method.

### Usage example
```php
// ...

try {
    $article = Article::find(123);

    $snapshot = $article->ledgers()->first()->extract(false);

} catch (AccountantException $e) {
    // Handle potential exception due to an invalid ledger, property or cipher implementation
}

// ...
```