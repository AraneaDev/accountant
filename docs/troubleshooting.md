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

### Testing and Command Line Interface
By default, Eloquent events fired from a **CLI** or **Testing** context, **will not** be recorded.

Refer to the [Recording Contexts](configuration.md#recording-contexts) section for more details.

## Return value of Altek\Accountant\Resolvers\UserResolver::resolve() must be an instance of Altek\Accountant\Contracts\Identifiable or null
This means the `User` model being returned by the `resolve()` method doesn't implement the `Identifiable` interface.

Refer to the [UserResolver](resolvers.md#user-resolver) documentation for more details.

## Attributes are considered modified, when they're not
False positives may give origin to `Ledger` records.
This happens when an Eloquent model with boolean/date attributes gets updated, regardless of change in those attributes.

For illustration purposes, this is how the internal state of the model would look like:

Current state (**$attributes** array):
- A `true` value will stay `true`
- A `false` value will stay `false`
- A `YYYY-MM-DD` format date remains `YYYY-MM-DD`

Previous state (**$original** array):
- A `true` value becomes `1`
- A `false` value becomes `0`
- A `YYYY-MM-DD` format date changes to `YYYY-MM-DD 00:00:00`

Under these circumstances, the `getDirty()` and `isDirty()` methods will give a false positive.
 
> **TIP:** This behaviour has been [fixed](https://github.com/laravel/framework/pull/18400) in Laravel 5.5+. For older versions of Laravel, use this [trait](https://gist.github.com/crashkonijn/7d581e55770d2379494067d8b0ce0f6d), courtesy of [Peter Klooster](https://github.com/crashkonijn)!

## Ledgers without modified values are being recorded
A `Ledger` is more than just the `modified` property value.

There's other metadata like `user_*`, `recordable_*`, `context`, `event`, `properties`, `extra`, `url`, `ip_address`, `user_agent` and `signature`, which should be more than enough for accountability purposes.

Nevertheless, if such information isn't of use to retain, just register the following observer in the `boot()` method of the `Ledger` model:

```php
static::creating(function (Ledger $model) {
    if (empty($model->modified)) {
        return false;
    }
});
```

> **CAVEAT:** Keep in mind that the `modified` column of a `retrieved` event, will always be empty!

## PHP Fatal error: Maximum function nesting level of '512' reached, aborting!
This might happen when a `User` model has the `retrieved` Eloquent event set as recordable, and a retrieval happens.

It boils down to the following:

1. A `User` record is retrieved from the database;
2. Given the `retrieved` event is recordable, the recording process kicks in;
3. During the data gathering phase for the new `Ledger`, the current user must be resolved for accountability purposes;
4. The current `User` record is retrieved after being resolved;
5. The previous step starts the process all over again from step **2**, leading to an infinite cycle;

### Solution #1
The simplest way to avoid this, is to disable the `retrieved` event from being recordable on a `User` model:

```php
<?php

namespace App\Models;

use Altek\Accountant\Contracts\Identifiable;
use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Identifiable, Recordable
{
    use \Altek\Accountant\Recordable;

    protected $recordableEvents = [
        'created',
        'updated',
        'deleted',
        'restored',
    ];

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getKey();
    }

    // ...
}
```

### Solution #2
Another way to work around this, is to implement a `UserResolver` where the logic to fetch a `User` relies on the `Illuminate\Database\Query\Builder`, which doesn't fire events.

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

> **TIP:** A custom `Ledger` model needs to be created with the above methods. Don't forget to [update](ledger-implementation.md#defining-the-ledger-model) the `Ledger` implementation in your configuration!

## IpAddressResolver incorrectly resolving IP addresses 
This usually happens to applications running behind a load balancer (or proxy), in which the IP address of the load balancer/proxy is being returned, instead.

Refer to the [IP Address Resolver](resolvers.md#ip-address-resolver) section for a workaround.
