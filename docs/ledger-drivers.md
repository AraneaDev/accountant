# Ledger Drivers
Drivers contain the logic to store `Recordable` model data.
Out of the box, the Accountant package includes a `Database` driver.

Besides storing model attribute changes and other metadata, drivers also handle pruning when a ledger threshold is set.

While the `Database` driver can be enough for most use cases, should you need to write a custom one, you can do so.

## Creating a custom Driver
A driver is just a class implementing the `LedgerDriver` interface.

To create a new driver, execute

```sh
php artisan make:ledger-driver MyCustomDriver
```

The previous command will create a file called `MyCustomDriver.php` in the `app/LedgerDrivers` folder with the following content:

```php
<?php

declare(strict_types=1);

namespace App\LedgerDrivers;

use Altek\Accountant\Contracts\Ledger;
use Altek\Accountant\Contracts\LedgerDriver;
use Altek\Accountant\Contracts\Recordable;

class MyCustomDriver implements LedgerDriver
{
    /**
     * Create a Ledger.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $event
     * @param string                                 $pivotRelation
     * @param array                                  $pivotProperties
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     *
     * @return \Altek\Accountant\Contracts\Ledger
     */
    public function record(
        Recordable $model,
        string $event,
        string $pivotRelation = null,
        array $pivotProperties = []
    ): Ledger {
        // TODO: Implement the recording logic
    }

    /**
     * Remove older ledgers that go over the threshold.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return bool
     */
    public function prune(Recordable $model): bool
    {
        // TODO: Implement the pruning logic
    }
}
```

::: tip
The `Database` driver is a good starting point to get ideas for a new custom driver implementation.
:::

## Enabling a custom driver
There are two ways to enable a custom driver.

### Globally
This is done in the `config/accountant.php` configuration file.

```php
return [

    'ledger' => [
        // ...

        'driver' => App\LedgerDrivers\MyCustomDriver::class,
    ],

    // ...
];
```

In this example, the `MyCustomDriver` is set as the default ledger driver for all the `Recordable` models.

### Locally
The value is set per `Recordable` model, by assigning the `FQCN` of the driver to the `$ledgerDriver` attribute.

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    /**
     * Custom Ledger Driver.
     *
     * @var \App\LedgerDrivers\MyCustomDriver
     */
    protected $ledgerDriver = App\LedgerDrivers\MyCustomDriver::class;

    // ...
}
```

::: tip
A locally defined driver **always** takes precedence over a globally defined one.
:::
