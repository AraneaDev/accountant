# Ledger Migration
Even though the default migration will cover most usage cases, the ledger table schema can be somewhat customised.

Here are some changes that can be performed without losing functionality.

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

> {tip} Read more about this in the [Configuration](configuration) section.

## Using UUID instead of auto-incrementing ids
Some developers prefer to use [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) instead of auto-incrementing identifiers.
Here is how the migration should look like for the `user` and `recordable` columns.

Update the `User` from
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

The `Recordable` should be updated from
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

> {note} Always ensure that the `user_*` and `recordable_*` column types match the ones in their respective tables.

## Values with more than 255 characters
While odd, on some occasions, User Agent values may go over the 255 character mark. To avoid such problems, update the column from `string`

```php
$table->string('user_agent')->nullable();
```

to `text`

```php
$table->text('user_agent')->nullable();
```

## JSON WHERE() clauses
The Laravel [Query Builder](https://laravel.com/docs/5.7/queries#json-where-clauses) supports querying JSON columns.
Given the `properties` and `modified` columns store JSON data as `TEXT`, the column types can be updated from

```php
$table->text('properties')->nullable();
$table->text('modified')->nullable();
```

to

```php
$table->json('properties')->nullable();
$table->json('modified')->nullable();
```

This will allow the user to perform additional data filtering.

> {tip} Not all RDBMS support this feature, so check before making changes!
