# Ledger Implementation
The default `Ledger` extends the traditional `Illuminate\Database\Eloquent\Model` class, but if that's not appropriate, a custom model can be implemented.

> **TIP:** `Ledger` models must implement the `Altek\Accountant\Contracts\Ledger` interface!

## MongoDB Ledger model example
Start by installing the [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) package:

```sh
composer require jenssegers/mongodb
```

## Implementation

```php
<?php

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

> **NOTICE:** The bulk of the `Ledger` logic is in the `Altek\Accountant\Ledger` trait.

## Defining a custom Ledger model
To use the custom `Ledger`, update the `accountant.ledger.implementation` configuration value with the implementation's `FQCN`:

```php
return [

    'ledger' => [
        'implementation' => App\Models\MongoLedger::class,
        
        // ...
    ],

    // ...
];
```
