# Recordable model setup
Setting up a `Recordable` model is just a matter of implementing the `Altek\Accountant\Contracts\Recordable` interface, and adding the `Altek\Accountant\Recordable` trait.

```php
<?php

declare(strict_types=1);

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

## Pivot events
Support for pivot events has been introduced in version **1.1.0**. To enable these events, the `altek/eventually` package needs to be installed

```sh
composer require altek/eventually
```

and the `\Altek\Eventually\Eventually` trait must be added to the required models for the events to fire.

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;
    use \Altek\Eventually\Eventually;

    // ...
}
```

> **TIP:** Refer to the [Events](recordable-configuration.md#events) section of the `Recordable` configuration for additional information.
