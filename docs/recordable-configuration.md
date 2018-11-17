# Recordable configuration 
When recording a `Ledger`, there might be different outcomes.
It will depend on what has been set globally, or on a per `Recordable` model basis.

## Ledger threshold
Out of the box, there's no limit to the number of `Ledger` records that are kept for a given `Recordable` model.
If needed, set the threshold configuration to a positive `int` of your choice, to keep `Ledger` records to a minimum.

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
This is done on a per `Recordable` model basis, by assigning an `int` value to the `$ledgerThreshold` attribute.

> **TIP:** A locally defined threshold **always** takes precedence over a globally defined one.

```php
<?php
namespace App;

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

> **TIP:** By default, the `accountant.ledger.threshold` value is set to `0` (zero), which stands for no limit.

## Ledger events
By default, only the `created`, `updated`, `deleted` and `restored` Eloquent events are recorded.

To change this behavior, update the events configuration with an `array` including the events of your choice.

### Globally
This is done in the `config/accountant.php` configuration file.

```php
return [

    'ledger' => [
        // ...

        'events' => [
            'deleted',
            'restored',
        ],
    
        // ...
    ],

    // ...
];
```

### Locally
This is done on a per `Recordable` model basis, by assigning an `array` value to the `$ledgerEvents` attribute.

> **TIP:** Locally defined events **always** take precedence over globally defined ones.

```php
<?php
namespace App;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    /**
     * Ledger events.
     *
     * @var array
     */
    protected $ledgerEvents = [
        'deleted',
        'restored',
    ];

    // ...
}
```

### Retrieved event
Eloquent version **5.5.0** introduced the `retrieved` event. While supported by this package, `retrieved` events are **not** recorded by default.

The reason is to prevent a **massive** amount of `Ledger` records, specially on busy applications, so enable it with care.

Keep in mind that when caching is active - and depending on how it's configured - the `retrieved` event might not fire as often!

> **TIP:** If tou get a **PHP Fatal error:  Maximum function nesting level of '512' reached, aborting!** after enabling the `retrieved` event, make sure to check the [troubleshooting](troubleshooting.md) guide for help. 

## Enable/Disable recording
The recording functionality can be enabled and disabled via two static methods.

Using the `Article` model from other examples, here is how it works:

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
