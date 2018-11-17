# Configuration
The default recording behavior can be modified by updating the settings in the `config/accountant.php` configuration file.

## Ledger implementation
By default, the package will use `Altek\Accountant\Models\Ledger`.

```php
return [
    
    'ledger' => [
        'implementation' => Altek\Accountant\Models\Ledger::class,
        
        // ...
    ],

    // ...
];
```

Read more about it in the [Ledger Implementation](ledger-implementation.md) section.

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

## Command Line Interface and Jobs
By default, Eloquent events from a **Job** or from the **CLI** (i.e. migrations, tests, commands, Tinker, ...), **WILL NOT** be recorded.
To enable CLI recording, set the `accountant.ledger.cli` value to `true`.

```php
return [

    'ledger' => [
        // ...
        
        'cli' => true,
        
        // ...
    ],

    // ...
];
```

> **NOTICE:** Resolving a `User` in the CLI may not work.

## User
This package supports multiple user types, by using a polymorphic `MorphTo` relation.

### Prefix
By default, the column names used are `user_id` and `user_type`. For a different user column prefix, change the configuration value and update the [migration](ledger-migration.md) accordingly.

```php
return [
    // ...

    'user' = [
        'prefix' => 'user',
    ],
];
```

> **NOTICE:** The `Ledger` **compile()** method will still use `user_id` and other `user_` prefixed keys for user data, regardless of the prefix set in the configuration.

### Auth Guards
Specify the authentication guards the `UserResolver` should use when trying to resolve a user.

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
