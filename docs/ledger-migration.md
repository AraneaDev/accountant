# Ledger Migration
Even though the default migration should cover most use cases, the ledger table schema allows for some customisation.

Here you'll find some of the changes that can be performed.

## Using a different prefix for the User ID/Type columns
Instead of the default `user_id` and `user_type` columns, a different prefix can be set:

```php
$table->nullableMorphs('accountable');
```

Also make sure the `accountant.user.prefix` value in the configuration reflects this change:

```php
return [
    // ...

    'user' = [
        'prefix' => 'accountable',
    ],
];
```

> **TIP:** Refer to the [User prefix](configuration.md#prefix) section for more details.

## Using UUID instead of auto-incrementing ids
Some developers prefer to use [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) instead of auto-incrementing identifiers.
Here is how the migration should look like for the `user` and `recordable` columns.

Update the `User` columns from
```php
$table->nullableMorphs('user');
```

to

```php
$table->uuid('user_id')->nullable();
$table->string('user_type')->nullable();
$table->index([
    'user_id', 
    'user_type',
]);
```

The `Recordable` columns should be updated from
```php
$table->morphs('recordable');
```

to

```php
$table->uuid('recordable_id');
$table->string('recordable_type');
$table->index([
    'recordable_id', 
    'recordable_type',
]);
```

> **NOTICE:** Always make sure the `user_*` and `recordable_*` column types match the ones in their respective tables.

## Values with more than 255 characters
While odd, on some occasions, User Agent values may go over the 255 character mark. To avoid such problems, update the column from
```php
$table->string('user_agent')->nullable();
```

to

```php
$table->text('user_agent')->nullable();
```

## Using JSON column type instead of TEXT
The Laravel [Query Builder](https://laravel.com/docs/5.7/queries#json-where-clauses) supports querying `JSON` type columns.

By default, the `properties`, `modified`, `pivot` and `extra` columns store JSON data as `TEXT`, but the column types can be updated from

```php
$table->text('properties');
$table->text('modified');
$table->text('pivot');
$table->text('extra');
```

to

```php
$table->json('properties');
$table->json('modified');
$table->json('pivot');
$table->json('extra');
```

This change allows for extended data filtering capabilities.

> **CAVEAT:** Not all RDBMS support this feature, so check before making changes!
