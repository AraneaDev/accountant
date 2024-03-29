# Ciphers
Using a cipher makes it easy to conceal sensitive information and/or store binary data, while recording a `Ledger`.

There are two supported types available, **one way** and **two way**.

## Bleach cipher
`Bleach` is a **one way** cipher that conceals around 90% of the data from left to right.

### Example
Input value                               | Output value
------------------------------------------|------------------------------------------
`Keeping Track Of Eloquent Model Changes` | `-----------------------------------nges`

### Usage
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Altek\Accountant\Ciphers\Bleach;
use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    /**
     * Ciphers.
     *
     * @var array
     */
    protected $ciphers = [
        'title' => Bleach::class,
    ];

    // ...
}
```

::: danger CAVEAT
Bear in mind that **one way** ciphered data cannot be reverted to its original form, which will affect operations such as the [Recordable Extraction](recordable-extraction.md)!
:::

## Base64 cipher
`Base64` is a **two way** cipher that encodes properties using the [Base64](https://en.wikipedia.org/wiki/Base64) encoding scheme.

It is specially useful when keeping track of binary data, which would otherwise break the [casting](https://laravel.com/docs/5.8/eloquent-mutators#array-and-json-casting) to JSON functionality of the underlying `Eloquent` model.

### Example
Input value                               | Output value
------------------------------------------|------------------------------------------
`Keeping Track Of Eloquent Model Changes` | `S2VlcGluZyBUcmFjayBPZiBFbG9xdWVudCBNb2RlbCBDaGFuZ2Vz`

### Usage
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Altek\Accountant\Ciphers\Base64;
use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    /**
     * Ciphers.
     *
     * @var array
     */
    protected $ciphers = [
        'title' => Base64::class,
    ];

    // ...
}
```

::: tip
Any **two way** ciphered property value will be returned in its original form by the `getData()` method.
:::

## Custom ciphers
If the included ciphers don't suit your needs, you can always roll your own.

A cipher is just a class implementing the `\Altek\Accountant\Contracts\Cipher` interface.

::: tip
You can generate a skeleton `Cipher` class with the `php artisan accountant:cipher` command. You can grab ideas from the included ciphers for your own implementation.
:::
