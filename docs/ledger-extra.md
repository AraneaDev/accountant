# Ledger Extra (data)
By overriding the `supplyExtra()` method on any `Recordable` model, the user can include extra data when recording a `Ledger`.

Three arguments are injected into the method:
- The Eloquent event name that was fired;
- All the `Recordable` model properties (after ciphering)
- Depending if the user was resolved or not, an `Identifiable` class or `null`;

```php
<?php

declare(strict_types=1);

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

The above example is quite simple, but the `extra` column can be used for all sorts of things (additional fields, notes, tagging, ...).

By default, the column type is defined as `TEXT` and the data is stored as a string of **JSON**, which can be fetched as an `array` property from the `Ledger` model.

> **TIP:** The user can take advantage of RDBMS that support the `JSON` column type, by updating the `ledgers` table migration accordingly. Refer to the [Ledger Table](ledger-table.md) documentation for more information.
