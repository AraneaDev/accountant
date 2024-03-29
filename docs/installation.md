# Installation
The easiest way to install the Accountant package, is through [Composer](https://getcomposer.org/doc/00-intro.md).
Executing the following command from your project root will get you the latest available version:

```sh
composer require altek/accountant
```

::: warning NOTICE
This package supports [Laravel](https://laravel.com/docs/5.8/) and [Lumen](https://lumen.laravel.com/docs/5.8/) from versions **5.2** and up.
:::

# Configuration
There's a slight difference between the **Laravel** and **Lumen** configurations, so we cover them both.

## Laravel
Edit the `config/app.php` file and add the following line to register the service provider:

```php
'providers' => [
    // ...

    Altek\Accountant\AccountantServiceProvider::class,

    // ...
],
```

::: tip
If you're using Laravel version **5.5** or greater, you can skip the service provider setup in favour of the [Package Auto-Discovery](https://laravel.com/docs/5.8/packages#package-discovery) feature.
:::

## Lumen
Add the following line to register the service provider in the `bootstrap/app.php` file:

```php
// ...

$app->register(Altek\Accountant\AccountantServiceProvider::class);

// ...
```

Also in the `bootstrap/app.php` file, enable `Facades` and `Eloquent`:

```php
// ...

$app->withFacades();

$app->withEloquent();

// ...
```

Finally, the configuration file must be loaded into the application by adding the following line to `bootstrap/app.php`:

```php
// ...

$app->configure('accountant');

// ...
```

The `vendor:publish` command is not natively available in **Lumen**, so an extra package must be installed:

```sh
composer require laravelista/lumen-vendor-publish
```

Once installed, register the new command in `app/Console/Kernel.php`:

```php
// ...

protected $commands = [
    \Laravelista\LumenVendorPublish\VendorPublishCommand::class,
];

// ...
```

::: warning NOTICE
The service provider registration is mandatory in order to publish the configuration and migration files!
:::

# Publishing
After your framework of choice has been configured, publish the configuration file using the following command:

```sh
php artisan vendor:publish --tag="accountant-configuration"
```

This will create the `config/accountant.php` configuration file.

You can read more about the available configuration settings in the [Configuration](configuration.md) section.

# Database
Publish the database migration files with the following command:

```sh
php artisan vendor:publish --tag="accountant-migrations"
```

## Customisation
If needed, the `ledgers` table can be customised.

Read more about it in the [Ledger Table](ledger-table.md) section.

## Migration
Once the previous steps has been done, execute the following `artisan` command to run the migration:

```sh
php artisan migrate
```

This will create the `ledgers` table in your database.

## User model
In order to use this package, the `User` model needs to implement the `Altek\Accountant\Contracts\Identifiable` interface.

Please refer to the [Identifiable implementation](resolvers.md#identifiable-implementation) section for details.

# Resolvers
Read more about this subject in the [Resolvers](resolvers.md) section!
