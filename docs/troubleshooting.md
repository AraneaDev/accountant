# Troubleshooting
A compilation of common problems and ways to work around them.

## Recording does not work
You followed the documentation to the letter and yet, `Ledger` records are not being created?

### Query Builder vs. Eloquent
Bear in mind that this package relies on Eloquent [events](https://laravel.com/docs/5.7/eloquent#events) and if they don't fire, a `Ledger` won't be created.

The most common mistake is performing an `update` or `delete` operation using a `Builder` instance, instead of an Eloquent `Model`.

Using the `Builder` won't create a `Ledger`:
```php
Article::where('id', $id)->update($data);
```

But using `Eloquent` will:
```php
Article::find($id)->update($data);
```

### Command Line Interface and Jobs
By default, Eloquent events from a **Job** or from the **CLI** (i.e. migrations, tests, commands, Tinker, ...), **WILL NOT** be recorded.

Please refer to the [Configuration](configuration.md) for more details. 

## Attributes are considered modified, when they're not
False positives may give origin to `Ledger` records.
This happens when an Eloquent model with boolean/date attributes gets updated, regardless of change in those attributes.

For illustration purposes, this is how the internal state of the model would look like:

Current state (**$attributes** array):
- `true` stays `true`
- `false` stays `false`
- `YYYY-MM-DD` stays `YYYY-MM-DD`

Previous state (**$original** array):
- `true` becomes `1`
- `false` becomes `0`
- `YYYY-MM-DD` becomes `YYYY-MM-DD 00:00:00`

That makes the `getDirty()` and `isDirty()` methods to give a false positive when comparing data.
 
> **TIP:** This behaviour has been [fixed](https://github.com/laravel/framework/pull/18400) in Laravel 5.5+. For older versions of Laravel, use this [trait](https://gist.github.com/crashkonijn/7d581e55770d2379494067d8b0ce0f6d), courtesy of [Peter Klooster](https://github.com/crashkonijn)!

Other discussions about this [subject](https://github.com/laravel/internals/issues/349).

## Empty modified values being recorded
A `Ledger` record is more than just the **modified** values.

There's metadata like `event`, `user_*`, `url`, `ip_address` and `user_agent`, which in some cases is more than enough for accountability purposes.

Still, if you don't want to keep track of such information when the `modified` is empty, register the following observer in the `Ledger` model's `boot()` method:

```php
Ledger::creating(function (Ledger $model) {
    if (empty($model->modified)) {
        return false;
    }
});
```

> **CAVEAT:** Keep in mind that the `modified` column of a `retrieved` event, will always be empty!

## PHP Fatal error:  Maximum function nesting level of '512' reached, aborting!
This error happens when a `Ledger` is being created for a `retrieved` event on a `User` model.
It boils down to the `UserResolver`, retrieving a `User` record, which will fire a new `retrieved` event, leading to a new resolve cycle and so on.

To avoid this, make sure the `User` model isn't configured to record on `retrieved` events, like so:

```php
<?php

namespace App;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;

    protected $ledgerEvents = [
        'created',
        'updated',
        'deleted',
        'restored',
    ];

    // ...
}
```

## Attribute accessors and modifiers are not applied to SoftDeleted models
Because not everyone uses the `SoftDeletes` trait, the `Ledger` relationships (`Recordable` and `User`) will return `null` by default, if any of those related records has been soft deleted.

To overcome this issue, the relation methods in the `Ledger` model must be updated to include trashed models:

```php
/**
 * {@inheritdoc}
 */
public function recordable()
{
    return $this->morphTo()->withTrashed();
}

/**
 * {@inheritdoc}
 */
public function user()
{
    return $this->morphTo()->withTrashed();
}
```

> **TIP:** A custom `Ledger` model needs to be created with the above methods. Don't forget to update the `Ledger` implementation in your configuration!

## IpAddressResolver incorrectly resolving IP addresses 
This usually happens to applications running behind a load balancer (or proxy), in which the IP address of the load balancer/proxy is being returned, instead.

Refer to the `IpAddressResolver` section in the [Ledger Resolvers](ledger-resolvers.md) documentation for a workaround.
