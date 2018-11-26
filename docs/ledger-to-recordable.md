# Ledger to Recordable
A `Ledger` is a snapshot of a `Recordable` model at a given point in time.
With the `toRecordable()` method, an exact `Recordable` instance can be recreated from a `Ledger`.

This method supports two modes of operation.

## Strict mode
This is the default, when the `toRecordable()` method is called without arguments.

In order to recreate an exact `Recordable` instance in this mode, **ALL** properties must be available their original form.

### Usage example
```php
// ...

try {
    $article = Article::find(123);

    $snapshot = $article->ledgers()->first()->toRecordable();

} catch (DecipherException $e) {
    // Handle potential exception while deciphering
} catch (AccountantException $e) {
    // Handle potential exception due to an invalid property/cipher implementation
}

// ...
```

> **CAVEAT:** The method will throw a `Altek\Accountant\Exceptions\DecipherException` if a property has been encoded by a **one way** cipher, which makes deciphering unfeasible.

## Soft mode
This is more easy-going approach, which will tolerate **one way** ciphered values by assigning them to `Recordable` properties.
 
To use this mode, pass `false` as the only argument to the `toRecordable()` method.

### Usage example
```php
// ...

try {
    $article = Article::find(123);

    $snapshot = $article->ledgers()->first()->toRecordable(false);

} catch (AccountantException $e) {
    // Handle potential exception due to an invalid property/cipher implementation
}

// ...
```
