# Ledger Extra (data)
By overriding the `supplyExtra()` method on any `Recordable` model, the user can pass extra data when recording a `Ledger`.

The method will have three arguments injected, which may come in handy.

These are the Eloquent event name that was fired, all the `Recordable` model properties (after ciphering) and depending if the logged user was resolved or not, an `Identifiable` class or `null`.

```php
<?php

namespace App\Models;

use Altek\Accountant\Contracts\Identifiable;
use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    // ...

    /**
     * {@inheritdoc}
     */
    public function supplyExtra(string $event, array $properties, ?Identifiable $user): array
    {
        return [
            'slug' => str_slug($properties['title']),
        ];
    }
}
```

The above example is quite simple, but the `extra` column in the `ledgers` table can be used for all sorts of things.

By default, the `extra` column has a `TEXT` type and the data will be stored as a `JSON` string.

The user can take advantage of RDBMS that support the `JSON` column type and update the `ledgers` table migration accordingly, to perform queries against JSON columns.

> **TIP:** Refer to the [Ledger Migration](ledger-migration.md) section for more information.
