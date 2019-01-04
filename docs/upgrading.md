# Upgrading
This document provides the necessary steps to successfully upgrade to the latest version.

## Upgrade from version 1.0.x to version 1.1.x
Version **1.1.x** introduces a new `pivot` column to store pivot event data and disables `NULL` values in the `properties`, `modified` and `extra` columns.

Make sure to publish the new migration files

```sh
php artisan vendor:publish --tag="accountant-migrations"
```

and execute the migrations afterward

```sh
php artisan migrate
```

For additional information about pivot event recording, refer to the [Events](recordable-configuration.md#events) section of the `Recordable` configuration.
