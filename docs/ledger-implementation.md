# Ledger Implementation
The default `Ledger` extends the traditional `Illuminate\Database\Eloquent\Model` class, but if that's not appropriate, a custom model can be implemented.

::: tip
`Ledger` models must implement the `Altek\Accountant\Contracts\Ledger` interface!
:::

## MongoDB Ledger example
Start by installing the [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) package:

```sh
composer require jenssegers/mongodb
```

## Implementation

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoLedger extends Model implements \Altek\Accountant\Contracts\Ledger
{
    use \Altek\Accountant\Ledger;

    /**
     * {@inheritdoc}
     */
    protected $table = 'ledgers';

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'properties' => 'json',
        'modified'   => 'json',
        'pivot'      => 'json',
        'extra'      => 'json',
    ];

    /**
     * {@inheritdoc}
     */
    public function recordable()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->morphTo();
    }
}
```

::: warning NOTICE
The bulk of the `Ledger` logic is in the `Altek\Accountant\Ledger` trait.
:::

## Enabling a custom Ledger
Set the `accountant.ledger.implementation` configuration value to the `FQCN` of the custom `Ledger` class:

```php
return [

    'ledger' => [
        'implementation' => App\Models\MongoLedger::class,
        
        // ...
    ],

    // ...
];
```
