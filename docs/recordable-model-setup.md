# Recordable model setup
Setting up a `Recordable` model could not be easier.
Just **implement** the `Altek\Accountant\Contracts\Recordable` interface and **use** the `Altek\Accountant\Recordable` trait.

```php
<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    // ...
}
```

The `Database` ledger driver will be used by default.

> **TIP:** If needed, a custom driver could be used instead. Read more about it in the [Ledger Driver](ledger-drivers.md) section.
