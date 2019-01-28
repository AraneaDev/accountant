# Configuration
This section describes some of the basic configuration settings of the package.

## Ledger implementation
The `Altek\Accountant\Models\Ledger` implementation is set by omission.

```php
return [
    
    'ledger' => [
        'implementation' => Altek\Accountant\Models\Ledger::class,
        
        // ...
    ],

    // ...
];
```

::: tip
Read the [Ledger Implementation](ledger-implementation.md) documentation for more details.
:::

## Ledger driver
The bundled `Database` driver is set as default.

```php
return [

    'ledger' => [
        // ...

        'driver' => 'database',
    ],

    // ...
];
```

::: tip
For custom implementation details, check the [Ledger Drivers](ledger-drivers.md) reference.
:::

## Recording Contexts
There are three recording contexts.

Name                   | Constant                         | Value | Resolved when running
-----------------------|----------------------------------|------:|----------------------------------------
Testing                | `Altek\Accountant\Context::TEST` | `1`   | PHPUnit, ...
Command Line Interface | `Altek\Accountant\Context::CLI`  | `2`   | Migrations, Jobs, Commands, Tinker, ...
Web                    | `Altek\Accountant\Context::WEB`  | `4`   | Apache, CGI, FPM, ...

By default, the package is set to record **only** in the `Altek\Accountant\Context::WEB` context.

To enable other contexts, set the `accountant.contexts` value to a context [bit mask](https://en.wikipedia.org/wiki/Mask_(computing)).

The following example promotes all the available contexts for recording.

```php
return [

    // ...
    
    'contexts' => Altek\Accountant\Context::TEST | Altek\Accountant\Context::CLI | Altek\Accountant\Context::WEB,

    // ...
];
```

To disable recording entirely, set the `accountant.contexts` value to zero:

```php
return [

    // ...
    
    'contexts' => 0,

    // ...
];
```

## User
This package supports multiple user types through the use of a `MorphTo` relation.

### Prefix
By default, the column names used are `user_id` and `user_type`. For a different user column prefix, change the configuration value, and update the [Ledger Table](ledger-table.md) accordingly.

```php
return [
    // ...

    'user' = [
        'prefix' => 'user',
    ],
];
```

### Auth Guards
Specify which authentication guards the default `UserResolver` should use when trying to resolve a user.

```php
return [
    // ...

    'user' = [
        // ...

        'guards' => [
            'web',
            'api',
        ],
    ],
];
```

::: tip
Refer to the [User Resolver](resolvers.md#user-resolver) section for additional information.
:::
