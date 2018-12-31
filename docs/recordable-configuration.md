# Recordable configuration 
This section describes the basic `Recordable` settings.

## Ledger threshold
Out of the box, there's no limit for the number of `Ledger` records that are kept for a given `Recordable` model.
If needed, set the threshold configuration to a positive `int` of your choice, to keep `Ledger` records to a minimum.

> **TIP:** By default, the `accountant.ledger.threshold` value is set to `0` (zero), which stands for no limit.

### Globally
This is done in the `config/accountant.php` configuration file.

```php
return [

    'ledger' => [
        // ...

        'threshold' => 10,
    
        // ...
    ],

    // ...
];
```

### Locally
The value is set per `Recordable` model, by assigning an `int` to the `$ledgerThreshold` attribute.

> **TIP:** A locally defined threshold **always** takes precedence over a globally defined one.

```php
<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    /**
     * Ledger threshold.
     *
     * @var int
     */
    protected $ledgerThreshold = 10;

    // ...
}
```

The above configuration, will keep the `10` latest `Ledger` records.

> **CAVEAT:** Bear in mind that pruning `Ledger` records will affect operations such as the [Data Integrity Check](data-integrity-check.md)!

## Events
[Eloquent events](https://laravel.com/docs/5.7/eloquent#events) are what trigger the recording of a `Ledger`, and by default, only the `created`, `updated`, `restored`, `deleted` and `forceDeleted` events are observed.

### Standard events
Event name              | Default state
------------------------|---------------
 `retrieved`            | **Disabled**
 `created`              | Enabled
 `updated`              | Enabled
 `restored`             | Enabled
 `deleted`              | Enabled
 `forceDeleted`         | Enabled

#### Retrieved event
From version **5.5.0**, Eloquent introduced the `retrieved` event. While supported, this event is **not** observed by default.

The rationale is to prevent large amounts of `Ledger` records, specially on busy applications, so enable it with care.

> **NOTICE:** When caching is active - and depending on how it's configured - the `retrieved` event might not fire as often!

> **TIP:** If tou get a **PHP Fatal error: Maximum function nesting level of '512' reached, aborting!** after enabling the `retrieved` event, check the [troubleshooting](troubleshooting.md#php-fatal-error-maximum-function-nesting-level-of-512-reached-aborting) guide for help. 

### Pivot events
Support for pivot event recording was introduced in version **1.1.0**.

Event name              | Default state
------------------------|---------------
 `toggled`              | **Disabled** 
 `synced`               | **Disabled**
 `existingPivotUpdated` | **Disabled**
 `attached`             | **Disabled**
 `detached`             | **Disabled**

To enable these events, the `altek/eventually` package needs to be installed

```sh
composer require altek/eventually
```

and the `\Altek\Eventually\Eventually` trait must be set in the required models.

> **CAVEAT:** The `sync()` and `toggle()` methods trigger multiple events, since they call `attach()` and `detach()` internally. You should only observe the `toggled` and `synced` **or** `attached` and `detached` events, to avoid multiple `Ledger` records for the same action.

### Event configuration
There are two ways to define which events should be observed.

#### Globally
This is done in the `config/accountant.php` configuration file.

```php
return [

    // ...

    'events' => [
        'retrieved',
        'created',
        'updated',
        'restored',
        'deleted',
        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ],

    // ...
];
```

#### Locally
The value is set per `Recordable` model, by assigning an `array` to the `$recordableEvents` attribute.

> **TIP:** Locally defined events **always** take precedence over globally defined ones.

```php
<?php

namespace App\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;
    use \Altek\Eventually\Eventually;

    /**
     * Recordable events.
     *
     * @var array
     */
    protected $recordableEvents = [
        'retrieved',
        'created',
        'updated',
        'restored',
        'deleted',
        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ];

    // ...
}
```

> **NOTICE:** The `\Altek\Eventually\Eventually` trait needs to be set in the model for the pivot events to fire.

## Enable/Disable recording
The `Recordable` trait provides two static methods to enable and disable the recording functionality.

```php
// Disable recording from this point on
Article::disableRecording();

// This operation won't be recorded
Article::create([
    // ...
]);

// Re-enable recording
Article::enableRecording();
```
