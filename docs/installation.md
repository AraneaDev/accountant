# Installation
The Accountant package should be installed using [Composer](http://getcomposer.org/doc/00-intro.md).
Running the following command from your project root, should get you the latest available version:

```sh
composer require altek/accountant
```

> **NOTICE:** This package supports **Laravel** and **Lumen** from version 5.2 onward.

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

> **TIP:** If you're on Laravel version **5.5** or higher, you can skip the service provider setup in favour of the Auto-Discovery feature.

## Lumen
Edit the `bootstrap/app.php` file and add the following line to register the service provider:

```php
// ...

$app->register(Altek\Accountant\AccountantServiceProvider::class);

// ...
```

`Facades` and `Eloquent` must also be enabled in the `bootstrap/app.php` file:

```php
// ...

$app->withFacades();

$app->withEloquent();

// ...
```

Configuration files must be explicitly loaded into the application by adding the following line to `bootstrap/app.php`.

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

> **CAVEAT:** The service provider registration is mandatory in order for the configuration to be published!

# Publishing
After your framework of choice has been configured, publish the configuration file with the following command:

```sh
php artisan vendor:publish --tag="accountant-configuration"
```

This will create the `config/accountant.php` configuration file.

You can read more about the available configuration settings in the [Configuration](configuration.md) section.

# Database
Publish the database migration file with the following command:

```sh
php artisan vendor:publish --tag="accountant-migration"
```

## Customisation
If needed, the `ledgers` table can be customised.

Read more about it in the [Ledger Migration](ledger-migration.md) section.

## Migration
Once the previous steps has been done, execute the following `artisan` command to run the migration:

```sh
php artisan migrate
```

This will create the `ledgers` table in your database.

# Resolvers
Read more about resolvers in the [Ledger Resolvers](ledger-resolvers.md) section!
