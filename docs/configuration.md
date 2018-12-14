# Configuration
The recording behavior can be modified in the configuration.

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

> **TIP:** Read the [Ledger Implementation](ledger-implementation.md) documentation, should you want to implement your own custom implementation.

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

> **TIP:** For custom implementation details, check the [Ledger Drivers](ledger-drivers.md) reference.

## Recording Contexts
There are three recording contexts.

Name                   | Constant                         | Value   | Resolved when running
-----------------------|----------------------------------|---------|----------------------------------------
Testing                | `Altek\Accountant\Context::TEST` | `0b001` | PHPUnit
Command Line Interface | `Altek\Accountant\Context::CLI`  | `0b010` | Migrations, Jobs, Commands, Tinker, ...
Web                    | `Altek\Accountant\Context::WEB`  | `0b100` | Apache, CGI, FPM, ...

By default, the package **only** records when in `WEB` context.

To enable additional contexts, set the `accountant.contexts` value to a [bit mask](https://en.wikipedia.org/wiki/Mask_(computing)).

The following example promotes all available contexts:

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
This package supports multiple user types by the use of a polymorphic `MorphTo` relation.

### Prefix
By default, the column names used are `user_id` and `user_type`. For a different user column prefix, change the configuration value, and update the [Ledger Migration](ledger-migration.md) accordingly.

```php
return [
    // ...

    'user' = [
        'prefix' => 'user',
    ],
];
```

### Auth Guards
Specify the authentication guards the `UserResolver` should use when resolving a user.

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

> **TIP:** Refer to the [User Resolver](resolvers.md#user-resolver) section for additional information.
