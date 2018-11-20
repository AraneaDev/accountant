# Ledger Implementation
The default `Ledger` extends the traditional `Illuminate\Database\Eloquent\Model` class, but if that's not appropriate, a custom one can be implemented.

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
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'properties' => 'json',
        'modified'   => 'json',
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

## Defining the Ledger model
This is how to set the above custom `MongoLedger` implementation in `config/accountant.php`:

```php
return [

    'ledger' => [
        'implementation' => App\Models\MongoLedger::class,
        
        // ...
    ],

    // ...
];
```

> **TIP:** If the value is missing from the configuration, the `ledgers()` relation method of the `Recording` trait will default to `Altek\Accountant\Models\Ledger`.
