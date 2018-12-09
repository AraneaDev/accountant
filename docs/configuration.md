# Configuration
The default recording behavior can be modified through several options in the `config/accountant.php` configuration file.

## Ledger implementation
By default, the `Altek\Accountant\Models\Ledger` implementation is set.

```php
return [
    
    'ledger' => [
        'implementation' => Altek\Accountant\Models\Ledger::class,
        
        // ...
    ],

    // ...
];
```

> **TIP:** Read the [Ledger Implementation](ledger-implementation.md) documentation, should you need to know how to implement your own custom implementation.

## Ledger driver
Being the only driver provided, the `Database` driver is set as default.

```php
return [

    'ledger' => [
        // ...

        'driver' => 'database',
    ],

    // ...
];
```

> **TIP:** For more information, check the [Ledger Drivers](ledger-drivers.md) documentation.

## Recording Contexts
There are three recording contexts.

Name                   | Constant                         | Value   | Resolved when running
-----------------------|----------------------------------|---------|----------------------------------------
Testing                | `Altek\Accountant\Context::TEST` | `0b001` | PHPUnit
Command Line Interface | `Altek\Accountant\Context::CLI`  | `0b010` | Migrations, Jobs, Commands, Tinker, ...
Web                    | `Altek\Accountant\Context::WEB`  | `0b100` | Apache, CGI, FPM, ...

By default, the package is set to record only in a **Web** context.

To enable additional contexts, set the `accountant.contexts` value to a [bit mask](https://en.wikipedia.org/wiki/Mask_(computing)).

The following example promotes all contexts:

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
This package supports multiple user types, by using a polymorphic `MorphTo` relation.

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

> **TIP:** Take a look at the `UserResolver` section in the [Resolvers](resolvers.md) documentation, for additional information.
