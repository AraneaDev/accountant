# Ledger table
Even though the default table schema covers most use cases, there's room for customisation.

Here you'll find some of the changes that can be performed.

## Migrations
### Default
Currently, the package ships with **three** migrations which, once executed, create the necessary table schema for the package to function properly.

While the `doctrine/dbal` is not a required dependency, it will be needed to run the default migrations, so make sure you have it.

```sh
composer require doctrine/dbal
```

The rationale is to avoid keeping an unnecessary dependency once the installation process is over.

### Custom
If you're just starting and prefer just one migration file, you can replace the default files with the [one](https://gitlab.com/altek/accountant/blob/master/tests/database/migrations/0000_00_00_000001_create_ledgers_test_table.php) included for testing.

::: tip
The `doctrine/dbal` dependency isn't required is you use this method.
:::

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

::: tip
Refer to the [User prefix](configuration.md#prefix) section for more details.
:::

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

::: warning NOTICE
Always make sure the `user_*` and `recordable_*` column types match the ones in their respective tables.
:::

## Values with more than 255 characters
While odd, on some occasions, User Agent values may go over the 255 character mark. To avoid insertion problems, update the column from
```php
$table->string('user_agent')->nullable();
```

to

```php
$table->text('user_agent')->nullable();
```

## Using JSON column type instead of TEXT
The Laravel [Query Builder](https://laravel.com/docs/5.8/queries#json-where-clauses) supports querying `JSON` type columns.

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

::: danger CAVEAT
Not all RDBMS support this feature, so check before making changes!
:::
