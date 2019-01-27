# Ledger Events
During and after the recording process, two events are fired.

## Recording
This first event is fired during the recording of a `Ledger`. If necessary, the recording can be aborted by returning `false` from the event listener's `handle()` method.

```php
<?php

declare(strict_types=1);

namespace App\Listeners;

use Altek\Accountant\Events\Recording;

class RecordingListener
{
    /**
     * Create the Recording event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the Recording event.
     *
     * @param \Altek\Accountant\Events\Recording $event
     * @return void
     */
    public function handle(Recording $event)
    {
        // Implement logic
    }
}
```

## Recorded
This second event is fired once a `Ledger` has been stored in the database.
In case some immediate action needs to take place after the event, this is where it should go.

```php
<?php

declare(strict_types=1);

namespace App\Listeners;

use Altek\Accountant\Events\Recorded;

class RecordedListener
{
    /**
     * Create the Recorded event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the Recorded event.
     *
     * @param \Altek\Accountant\Events\Recorded $event
     * @return void
     */
    public function handle(Recorded $event)
    {
        // Implement logic
    }
}
```

::: tip
For more information about events, check Laravel's official [documentation](https://laravel.com/docs/5.7/events)!
:::
