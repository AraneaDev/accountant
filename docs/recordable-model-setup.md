# Recordable model setup
Setting up a `Recordable` model is just a matter of implementing the `Altek\Accountant\Contracts\Recordable` interface.
The use of the `Altek\Accountant\Recordable` trait is also advised, since it contains the actual implementation.

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

If no changes are made to the configuration, the `Database` driver will be used by default.

> **TIP:** Refer to the [Ledger Driver](ledger-drivers.md) documentation, for alternatives.
